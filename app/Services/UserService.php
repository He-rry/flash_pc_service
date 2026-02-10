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

   private function syncExtraPermissions(User $user, $roleName, array $submittedPermissions)
{
    // ၁။ Cache ကို အရင်ရှင်းထုတ်လိုက်ပါ (အရေးကြီးဆုံး)
    app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

    // ၂။ လက်ရှိ Role ရဲ့ Permission တွေကို ယူပါ
    $role = \Spatie\Permission\Models\Role::where('name', $roleName)->first();
    $rolePermissions = $role ? $role->permissions->pluck('name')->toArray() : [];

    // ၃။ Logic: Form ကနေ တက်လာတဲ့အထဲမှာ Role Permission တွေနဲ့ မတူတာ (အပိုတွေ) ကိုပဲ ယူမယ်
    // ဒါပေမဲ့ array_values သုံးပြီး index ပြန်စီပေးဖို့ လိုပါတယ်
    $extraPermissions = array_values(array_diff($submittedPermissions, $rolePermissions));

    // ၄။ User ရဲ့ Direct Permission တွေကို အသစ်နဲ့ အကုန်အစားထိုးမယ်
    // ဒီကောင်က အဟောင်းကို ရှင်းပြီးသားဖြစ်လို့ Checkbox တွေ အသေဖြစ်မနေတော့ပါဘူး
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
            throw new \Exception("You cannot delete your own account.");
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
            throw new \Exception("You cannot permanently delete your own account.");
        }
        $user = User::withTrashed()->findOrFail($id);
        $userName = $user->name;
        $this->logActivity('FORCE_DELETE_USER', "Permanently deleted user: {$userName}");
        return $user->forceDelete();
    }
}
