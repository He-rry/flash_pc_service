<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Repositories\UserRepository;
use App\Services\UserService;
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
        
        // Security: manage-users permission ရှိသူသာ ဝင်ခွင့်ပြုမည်
        $this->middleware('can:manage-users');
    }

    public function index()
    {
        // Deleted users များကိုပါ မြင်နိုင်ရန် withTrashed() ထည့်ထားပါသည်
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

        $this->userService->createAdminUser($data);
        return redirect()->route('admin.users.index')->with('success', 'User created successfully');
    }

    public function edit(User $user)
    {
        Gate::authorize('manage', $user);

        $roles = Role::all();
        $userRoles = $user->roles->pluck('name')->toArray();
        $userPermissions = $user->getAllPermissions()->pluck('name')->toArray();
        $matrix = $this->getPermissionMatrix();
        
        $rolePerms = [];
        foreach ($roles as $role) {
            $rolePerms[$role->name] = $role->permissions->pluck('name')->toArray();
        }

        return view('admin.users.edit', compact('user', 'roles', 'userRoles', 'userPermissions', 'matrix', 'rolePerms'));
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

        // Input field mapping ညှိနှိုင်းခြင်း
        $data['custom_permissions'] = $request->input('permissions', []);

        $this->userService->updateAdminUser($user, $data);
        return redirect()->route('admin.users.index')->with('success', 'User updated successfully!');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        Gate::authorize('delete', $user);

        try {
            $this->userService->deleteUser($id);
            return redirect()->route('admin.users.index')->with('success', 'User has been deleted (Soft Delete).');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
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
            return redirect()->back()->with('success', 'User has been permanently deleted.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function getRolePermissions($roleName)
    {
        $role = Role::where('name', $roleName)->with('permissions')->first();
        return response()->json($role ? $role->permissions->pluck('name') : []);
    }
}