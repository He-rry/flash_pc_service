<?php

namespace App\Providers;

use App\Models\User;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate; // Gate ကို သုံးဖို့ import လုပ်ပါ
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Register repository bindings in a dedicated provider
        $this->app->register(\App\Providers\RepositoryServiceProvider::class);
    }

    public function boot(): void
    {
        // ✅ User Model နဲ့ UserPolicy ကို ဒီမှာ ချိတ်ဆက်ပေးရပါမယ်
        Gate::policy(User::class, UserPolicy::class);

        // ✅ အရှေ့မှာ ပြောခဲ့တဲ့ Super Admin Gate (Super Admin ဆို အကုန်ရစေချင်ရင်)
        Gate::before(function ($user, $ability) {
            return $user->hasRole('super-admin') ? true : null;
        });
    }
}
