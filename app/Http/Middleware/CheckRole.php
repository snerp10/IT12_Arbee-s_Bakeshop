<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        \Log::info("CheckRole middleware called with role: $role");
        
        if (!auth()->check()) {
            \Log::info('User not authenticated, redirecting to login');
            return redirect()->route('login');
        }

        $user = auth()->user();
        \Log::info("Authenticated user role: {$user->role}, required role: $role");
        
        if ($user->role !== $role) {
            \Log::info("Role mismatch, redirecting based on user role: {$user->role}");
            // Redirect to appropriate dashboard based on user's actual role
            switch ($user->role) {
                case 'admin':
                    return redirect('/dashboard/admin');
                case 'manager':
                    return redirect('/dashboard/manager');
                case 'baker':
                    return redirect('/dashboard/baker');
                case 'cashier':
                    return redirect('/dashboard/cashier');
                default:
                    return redirect()->route('login');
            }
        }

        \Log::info('Role matches, proceeding to route');
        return $next($request);
    }
}
