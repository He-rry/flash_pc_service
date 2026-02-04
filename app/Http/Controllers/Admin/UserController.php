<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct()
    {
        // Use Gate check so `super-admin` Gate::before still applies
        $this->middleware('can:manage-users');
    }

    public function index()
    {
        $users = User::paginate(15);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::all();

        // Define modules and actions for permission matrix
        $modules = [
            'shops' => 'Shops',
            'routes' => 'Routes',
            'logs' => 'Logs',
            'services' => 'Services',
            'users' => 'Users'
        ];

        $actions = ['view', 'create', 'update', 'delete', 'import', 'export'];

        // Ensure permissions exist in DB for matrix (non-destructive)
        $matrix = [];
        foreach ($modules as $key => $label) {
            foreach ($actions as $act) {
                $permName = "$act-$key";
                Permission::firstOrCreate(['name' => $permName]);
                $matrix[$key][] = ['name' => $permName, 'label' => ucfirst($act)];
            }
        }

        // Prepare role -> permissions map for client-side convenience
        $rolePerms = [];
        foreach ($roles as $role) {
            $rolePerms[$role->name] = $role->permissions->pluck('name')->toArray();
        }

        return view('admin.users.create', compact('roles', 'matrix', 'rolePerms'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'nullable|string|exists:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string'
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'] ?? null,
        ]);

        // Set primary role (single primary role)
        if (!empty($data['role'])) {
            $user->syncRoles([$data['role']]);
        }

        // Sync direct permissions (create missing permissions if necessary)
        $direct = $request->input('permissions', []);
        foreach ($direct as $p) {
            Permission::firstOrCreate(['name' => $p]);
        }
        $user->syncPermissions($direct);

        return redirect()->route('admin.users.index')->with('success', 'User created');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all();

        $modules = [
            'shops' => 'Shops',
            'routes' => 'Routes',
            'logs' => 'Logs',
            'services' => 'Services',
            'users' => 'Users'
        ];
        $actions = ['view', 'create', 'update', 'delete', 'import', 'export'];

        $matrix = [];
        foreach ($modules as $key => $label) {
            foreach ($actions as $act) {
                $permName = "$act-$key";
                Permission::firstOrCreate(['name' => $permName]);
                $matrix[$key][] = ['name' => $permName, 'label' => ucfirst($act)];
            }
        }

        $rolePerms = [];
        foreach ($roles as $role) {
            $rolePerms[$role->name] = $role->permissions->pluck('name')->toArray();
        }

        // Direct permissions of user
        $directPerms = $user->getDirectPermissions()->pluck('name')->toArray();

        return view('admin.users.edit', compact('user', 'roles', 'matrix', 'rolePerms', 'directPerms'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6|confirmed',
            'role' => 'nullable|string|exists:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string'
        ]);

        $user->name = $data['name'];
        $user->email = $data['email'];
        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }
        $user->role = $data['role'] ?? $user->role;
        $user->save();

        // Sync primary role
        if (!empty($data['role'])) {
            $user->syncRoles([$data['role']]);
        } else {
            $user->syncRoles([]);
        }

        // Sync direct permissions (create if missing)
        $direct = $request->input('permissions', []);
        foreach ($direct as $p) {
            Permission::firstOrCreate(['name' => $p]);
        }
        $user->syncPermissions($direct);

        return redirect()->route('admin.users.index')->with('success', 'User updated');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted');
    }
}
