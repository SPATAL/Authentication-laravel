<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Usage: 'role:admin' or 'role:user'
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role  Role to check against
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $role)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user();

        if ($role === 'admin') {
            // Only admins can access admin routes
            if ($user->role !== 'admin') {
                return redirect('/home')->with('error', "You don't have admin access.");
            }
            return $next($request);
        }

        if ($role === 'user') {
            // Both users and admins can access user routes
            if (!in_array($user->role, ['user', 'admin'])) {
                return redirect('/home')->with('error', "You don't have user access.");
            }
            return $next($request);
        }

        // If role is not recognized, deny access
        return redirect('/home')->with('error', "Access denied.");
    }
}
