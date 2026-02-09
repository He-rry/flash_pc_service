<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, Notifiable, HasRoles, HasFactory, SoftDeletes;

    /**
     * Role Constants များ သတ်မှတ်ခြင်း
     */
    public const ROLE_SUPER_ADMIN = 'super-admin'; // Spatie name convention နှင့် ညှိရန် - သုံးထားပါက
    public const ROLE_MANAGER = 'manager';
    public const ROLE_EDITOR = 'editor';
    public const ROLE_LOG_MANAGER = 'log-manager';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relationships
     */
    public function addedShops()
    {
        return $this->hasMany(Shop::class, 'added_by');
    }

    /**
     * Helper Methods for Role Checking
     * Spatie hasRole() ကို wrapper လုပ်ပေးထားခြင်းဖြစ်ပါသည်
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole(self::ROLE_SUPER_ADMIN);
    }

    public function isManager(): bool
    {
        return $this->hasRole(self::ROLE_MANAGER);
    }

    public function isEditor(): bool
    {
        return $this->hasRole(self::ROLE_EDITOR);
    }

    public function isLogManager(): bool
    {
        return $this->hasRole(self::ROLE_LOG_MANAGER);
    }
}