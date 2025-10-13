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
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $validEmail = 'washwise00@gmail.com';
        $validPassword = 'washwise123';

        if (
            $credentials['email'] === $validEmail &&
            $credentials['password'] === $validPassword
            ) {
            Session::put('authenticated', true);
            return redirect()->route('admindashboard');
            }

            return back()->withErrors([
                'email' => 'Invalid email or password.',
            ]);
    }

    public function logout(Request $request)
    {
        Session::forget('authenticated');
        return redirect()->route('loginAdmin');
    }
}
