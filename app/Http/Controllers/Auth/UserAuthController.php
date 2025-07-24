<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class UserAuthController extends Controller
{
    public function showLoginForm() 
    {
        return view('auth.login');
    }

    public function login(Request $request) {
        $credentials = $request->only('email', 'password');
        if (Auth::guard('web')->attempt($credentials)) {
            return redirect()->route('home');
        }
        return back()->withErrors(['email' => 'Invalid credentials']);
    }

    public function logout() {
        Auth::guard('web')->logout();
        return redirect()->route('user.login');
    }

    public function showRegisterForm() {
    return view('auth.register');
}

public function register(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users',
        'password' => 'required|confirmed|min:6',
    ]);

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => bcrypt($request->password),
    ]);

    Auth::guard('web')->login($user);

    return redirect()->route('home');
}
}
