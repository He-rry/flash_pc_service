<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;

class UserService
{
    protected $userRepo;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    // Controller Matrix
    public function getPermissionMatrix()
    {
        $modules = ['shops' => 'Shops', 'routes' => 'Routes', 'logs' => 'Logs', 'services' => 'Services', 'users' => 'Users', 'settings' => 'Settings'];
        $actions = ['view', 'create', 'update', 'delete', 'import', 'export'];
        $matrix = [];

        foreach ($modules as $key => $label) {
            foreach ($actions as $act) {
                $permName = "$act-$key";
                if (Permission::where('name', $permName)->exists()) {
                    $matrix[$label][] = ['name' => $permName, 'label' => ucfirst($act)];
                }
            }
        }
        return $matrix;
    }
   //Mapping role for js
    public function getRolePermissionsMap()
    {
        $roles = $this->userRepo->getAllRoles();
        $rolePerms = [];
        foreach ($roles as $role) {
            $rolePerms[$role->name] = $role->permissions->pluck('name')->toArray();
        }
        return $rolePerms;
    }

    public function createAdminUser(array $data)
    {
        $userData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ];
        $user = $this->userRepo->create($userData);
        // Role Assign
        $user->assignRole($data['role']);

        // Permission Sync
        if ($data['role'] !== 'super-admin') {
            $this->syncExtraPermissions($user, $data['role'], $data['custom_permissions'] ?? []);
        } else {
            $user->syncPermissions([]);
        }

        $this->userRepo->logActivity('CREATE_USER', "Created user: {$user->name} with role: {$data['role']}");

        return $user;
    }

    public function updateAdminUser(User $user, array $data)
    {
        $updateData = [
            'name' => $data['name'],
            'email' => $data['email'],
        ];

        if (!empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }
        $this->userRepo->update($user, $updateData);
        $user->syncRoles([$data['role']]);
        if ($data['role'] !== 'super-admin') {
            $this->syncExtraPermissions($user, $data['role'], $data['custom_permissions'] ?? []);
        } else {
            $user->syncPermissions([]);
        }
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->userRepo->logActivity('UPDATE_USER', "Updated user: {$user->name}");

        return $user;
    }

    private function syncExtraPermissions(User $user, $roleName, array $submittedPermissions)
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $rolePermissions = $this->userRepo->getPermissionsByRoleName($roleName)->toArray();
        $extraPermissions = array_values(array_diff($submittedPermissions, $rolePermissions));
        $user->syncPermissions($extraPermissions);
    }

    public function deleteUser($id)
    {
        if (Auth::id() == $id) throw new \Exception("You cannot delete your own account.");
        
        $user = $this->userRepo->find($id);
        $this->userRepo->delete($user);
        $this->userRepo->logActivity('DELETE_USER', "Deleted user: {$user->name}");
        return true;
    }

    public function restoreUser($id)
    {
        $user = $this->userRepo->restore($id);
        $this->userRepo->logActivity('RESTORE_USER', "Restored user: {$user->name}");
        return $user;
    }

    public function forceDeleteUser($id)
    {
        if (Auth::id() == $id) throw new \Exception("You cannot permanently delete your own account.");
        
        $user = User::withTrashed()->findOrFail($id); // For logging name before delete
        $name = $user->name;
        $this->userRepo->forceDelete($id);
        $this->userRepo->logActivity('FORCE_DELETE_USER', "Permanently deleted user: {$name}");
        return true;
    }
}