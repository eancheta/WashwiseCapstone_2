<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EnsureUserIsVerified
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();

            // normalize status
            $status = $user->status === null ? '' : trim(strtolower($user->status));

            if ($status !== 'verified') {
                // optional log for debugging
                Log::warning('Blocking login for unverified user', [
                    'id' => $user->id,
                    'email' => $user->email,
                    'status' => $user->status,
                ]);

                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->withErrors([
                    'email' => 'Your account is not verified. Please check your email.'
                ]);
            }
        }

        return $next($request);
    }
}
