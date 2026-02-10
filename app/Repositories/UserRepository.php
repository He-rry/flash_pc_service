<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserRepository
{
    // User List with Pagination
    public function getPaginatedUsers($perPage = 15)
    {
        return User::withTrashed()->with('roles')->latest()->paginate($perPage);
    }

    // Find User
    public function find($id)
    {
        return User::withTrashed()->findOrFail($id);
    }

    // Create User
    public function create(array $data)
    {
        return User::create($data);
    }

    // Update User
    public function update(User $user, array $data)
    {
        $user->update($data);
        return $user;
    }

    // Delete User
    public function delete(User $user)
    {
        return $user->delete();
    }

    // Restore User
    public function restore($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();
        return $user;
    }

    // Force Delete User
    public function forceDelete($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        return $user->forceDelete();
    }

    // Activity Log
    public function logActivity($action, $description)
    {
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'module' => 'USER_MANAGEMENT',
            'description' => $description,
            'ip_address' => request()->ip(),
        ]);
    }

    // Role
    public function getAllRoles()
    {
        return Role::all();
    }
    public function getAllPermissions()
    {
        return Permission::all();
    }
    public function getPermissionsByRoleName($roleName)
    {
        $role = Role::where('name', $roleName)->with('permissions')->first();
        return $role ? $role->permissions->pluck('name') : collect([]);
    }
}
