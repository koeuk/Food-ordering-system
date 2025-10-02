<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        
        foreach ($roles as $role) {
            if ($user->role === $role) {
                return $next($request);
            }
        }

        // If user doesn't have any of the required roles, redirect based on their role
        switch ($user->role) {
            case 'customer':
                return redirect()->route('dashboard.customer');
            case 'manager':
                return redirect()->route('dashboard.manager');
            case 'kitchen':
                return redirect()->route('dashboard.kitchen');
            case 'supplier':
                return redirect()->route('dashboard.supplier');
            default:
                return redirect()->route('dashboard');
        }
    }
}
