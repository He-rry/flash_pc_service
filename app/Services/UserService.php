<?php

namespace App\Services;

use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserService
{
    public function createAdminUser(array $data)
    {
        //  User Create 
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
        $user->assignRole($data['role']);
        if ($data['role'] !== 'super-admin') {
            $this->syncExtraPermissions($user, $data['role'], $data['custom_permissions'] ?? []);
        } else {
            $user->syncPermissions([]);
        }

        $this->logActivity('CREATE_USER', "Created user: {$user->name} with role: {$data['role']}");

        return $user;
    }

    /**
     * Edit Admin
     */
    public function updateAdminUser(User $user, array $data)
    {
        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
        ]);

        // Role  Sync
        $user->syncRoles([$data['role']]);

        // Super Admin Extra Permissions Sync
        if ($data['role'] !== 'super-admin') {
            $this->syncExtraPermissions($user, $data['role'], $data['custom_permissions'] ?? []);
        } else {
            // Super Admin  Permission clear
            $user->syncPermissions([]);
        }

        $this->logActivity('UPDATE_USER', "Updated user: {$user->name}");

        return $user;
    }

    //helper for permissions sync
    private function syncExtraPermissions(User $user, $roleName, array $submittedPermissions)
    {
        // Current Role  Permission
        $role = \Spatie\Permission\Models\Role::where('name', $roleName)->first();
        $rolePermissions = $role ? $role->permissions->pluck('name')->toArray() : [];

        // Form Diff Permission Role
        $extraPermissions = array_diff($submittedPermissions, $rolePermissions);

        // Extra  User  Direct Permission
        $user->syncPermissions($extraPermissions);
    }
    /**
     * Activity Log 
     */
    private function logActivity($action, $description)
    {
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'module' => 'USER_MANAGEMENT',
            'description' => $description,
            'ip_address' => request()->ip(),
        ]);
    }
    public function deleteUser($id)
    {
        if (Auth::id() == $id) {
            throw new \Exception("မိမိအကောင့်ကို မိမိပြန်ဖျက်၍ မရနိုင်ပါ။");
        }
        $user = User::findOrFail($id);
        $userName = $user->name;
        $user->delete();
        $this->logActivity('DELETE_USER', "Deleted user: {$userName}");
        return true;
    }
    public function restoreUser($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();
        $this->logActivity('RESTORE_USER', "Restored user: {$user->name}");

        return $user;
    }
    public function forceDeleteUser($id)
    {
        if (Auth::id() == $id) {
            throw new \Exception("မိမိအကောင့်ကို မိမိအပြီးတိုင် ဖျက်၍ မရနိုင်ပါ။");
        }
        $user = User::withTrashed()->findOrFail($id);
        $userName = $user->name;
        $this->logActivity('FORCE_DELETE_USER', "Permanently deleted user: {$userName}");
        return $user->forceDelete();
    }
}
