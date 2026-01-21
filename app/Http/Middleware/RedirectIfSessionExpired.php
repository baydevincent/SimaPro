<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfSessionExpired
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip checking for login, logout, and other auth routes
        $skipPaths = [
            'login',
            'logout',
            'password/*',
            'register',
            'forgot-password',
            'reset-password/*',
        ];

        foreach ($skipPaths as $path) {
            if ($request->is($path) ||
                ($request->isMethod('post') && str_contains($request->path(), 'login'))) {
                return $next($request);
            }
        }

        // Check if user is authenticated
        if (Auth::check()) {
            // Check if session has expired
            // We can check for a specific session variable that indicates last activity
            $lastActivity = Session::get('last_activity');

            if ($lastActivity) {
                $maxInactiveTime = config('session.lifetime') * 60; // Convert minutes to seconds

                // Check if current time exceeds the max inactive time
                if ((time() - $lastActivity) > $maxInactiveTime) {
                    // Session has expired due to inactivity
                    Auth::logout();
                    Session::invalidate();
                    Session::regenerateToken();

                    // Redirect to login with message
                    return redirect()->route('login')
                        ->with('error', 'Sesi Anda telah habis karena tidak ada aktivitas. Silakan login kembali.');
                }
            }

            // Update last activity time
            Session::put('last_activity', time());
        }

        return $next($request);
    }
}
