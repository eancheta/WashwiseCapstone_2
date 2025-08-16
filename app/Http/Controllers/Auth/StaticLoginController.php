<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;

class StaticLoginController extends Controller
{
    public function showLogin()
    {
        return Inertia::render('auth/LoginAdmin');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $validUsername = 'admin';
        $validPassword = 'secret123';

        if (
            $credentials['username'] === $validUsername &&
            $credentials['password'] === $validPassword
        ) {
            Session::put('admin_authenticated', true);
            return redirect()->route('admindashboard');
        }

        return back()->withErrors([
            'username' => 'Invalid username or password.',
        ])->withInput();
    }

    public function logout(Request $request)
    {
        Session::forget('admin_authenticated');
        return redirect()->route('loginAdmin');
    }
}
