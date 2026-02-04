<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::user();
            if ($user->isLogManager()) {
                return redirect()->route('admin.logs.index');
            }
            if ($user->isEditor()) {
                return redirect()->route('admin.shops.create');
            }
            return redirect()->route('admin.services.index');
        }

        return back()->withErrors(['email' => 'အချက်အလက် မှားယွင်းနေပါသည်။']);
    }
    public function logout(Request $request)
    {
        Auth::logout();

        // Session တွေကို အကုန်ဖျက်ပြီး Token အသစ်ပြောင်း
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Logged out successfully!');
    }
}
