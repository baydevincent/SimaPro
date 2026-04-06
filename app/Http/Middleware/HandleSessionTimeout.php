<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HandleSessionTimeout
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            // Check if session has expired
            if ($request->session()->has('last_activity')) {
                $lastActivity = $request->session()->get('last_activity');
                $idleTime = time() - $lastActivity;
                $maxIdleTime = config('session.lifetime') * 60; // Convert to seconds
                
                if ($idleTime > $maxIdleTime) {
                    Auth::logout();
                    
                    if ($request->ajax()) {
                        return response()->json([
                            'message' => 'Session expired',
                            'redirect' => route('login')
                        ], 401);
                    }
                    
                    return redirect()->route('login')
                        ->with('message', 'Sesi Anda telah berakhir. Silakan login kembali.');
                }
            }
            
            // Update last activity
            $request->session()->put('last_activity', time());
        }
        
        return $next($request);
    }
}
