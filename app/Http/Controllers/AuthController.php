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

            // Role to Dashboard Redirect
            return match (true) {
                $user->hasRole('log-manager') => redirect()->route('admin.logs.index'),
                $user->hasRole('editor')      => redirect()->route('admin.shops.create'),
                default                       => redirect()->route('admin.dashboard'),
            };
        }

        return back()->withErrors(['email' => 'အချက်အလက် မှားယွင်းနေပါသည်။']);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Logged out successfully!');
    }
}