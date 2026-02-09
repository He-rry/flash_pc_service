<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Repositories\UserRepository;
use App\Services\UserService; // UserService class ရှိရန်လိုအပ်ပါသည်
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    protected $userRepo;
    protected $userService;

    public function __construct(UserRepository $userRepo, UserService $userService)
    {
        $this->userRepo = $userRepo;
        $this->userService = $userService;
    }

    public function index()
    {
        $users = User::withTrashed()->with('roles')->latest()->paginate(15);
        return view('admin.users.index', compact('users'));
    }
    private function getPermissionMatrix()
    {
        $modules = [
            'shops'    => 'Shops',
            'routes'   => 'Routes',
            'logs'     => 'Logs',
            'services' => 'Services',
            'users'    => 'Users',
            'settings' => 'Settings'
        ];

        $actions = ['view', 'create', 'update', 'delete', 'import', 'export'];

        $matrix = [];
        foreach ($modules as $key => $label) {
            foreach ($actions as $act) {
                $permName = "$act-$key";
                // DB ထဲမှာရှိတဲ့ Permission တွေကိုပဲ matrix ထဲထည့်ပါမယ် (အသစ်ထပ်မဆောက်တော့ပါ)
                if (Permission::where('name', $permName)->exists()) {
                    $matrix[$label][] = [
                        'name' => $permName,
                        'label' => ucfirst($act)
                    ];
                }
            }
        }
        return $matrix;
    }

    public function create()
    {
        $roles = Role::all();
        $matrix = $this->getPermissionMatrix();
        $permissions = Permission::all();

        // Client-side JS အတွက် Role တစ်ခုချင်းစီ၏ Permissions များကို Map လုပ်ထားခြင်း
        $rolePerms = [];
        foreach ($roles as $role) {
            $rolePerms[$role->name] = $role->permissions->pluck('name')->toArray();
        }

        return view('admin.users.create', compact('roles', 'matrix', 'permissions', 'rolePerms'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|string|exists:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string'
        ]);

        // Service class ကိုအသုံးပြု၍ User ကို Create လုပ်ပါသည်
        $this->userService->createAdminUser($data);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully');
    }

    public function edit(User $user)
    {
        Gate::authorize('manage', $user);

        $roles = Role::all();

        // ၁။ System ထဲမှာ ရှိသမျှ Permission အားလုံးကို ယူရပါမယ် (View ထဲမှာ loop ပတ်ဖို့)
        $permissions = \Spatie\Permission\Models\Permission::all();

        $userRoles = $user->roles->pluck('name')->toArray();

        // Role မှရသော permission ကော၊ direct ပေးထားသော permission ကော အားလုံးယူခြင်း
        $userPermissions = $user->getAllPermissions()->pluck('name')->toArray();
        $matrix = $this->getPermissionMatrix();

        $rolePerms = [];
        foreach ($roles as $role) {
            $rolePerms[$role->name] = $role->permissions->pluck('name')->toArray();
        }
        return view('admin.users.edit', compact(
            'user',
            'roles',
            'permissions',
            'userRoles',
            'userPermissions',
            'matrix',
            'rolePerms'
        ));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6|confirmed',
            'role' => 'required|string|exists:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string'
        ]);

        $data['custom_permissions'] = $request->input('custom_permissions', []);
        $this->userService->updateAdminUser($user, $data);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully!');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Policy
        Gate::authorize('delete', $user);
        try {
            $this->userService->deleteUser($id);
            return redirect()->route('admin.users.index')->with('success', 'User has been deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * AJAX အတွက် Role အလိုက် Permission များကို json ပြန်ပေးရန်
     */
    public function getRolePermissions($roleName)
    {
        $role = Role::where('name', $roleName)->with('permissions')->first();
        return response()->json($role ? $role->permissions->pluck('name') : []);
    }
    public function restore($id)
    {
        try {
            $this->userService->restoreUser($id);
            return redirect()->back()->with('success', 'User has been restored successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function forceDelete($id)
    {
        try {
            $this->userService->forceDeleteUser($id);
            return redirect()->back()->with('success', 'User has been permanently deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
