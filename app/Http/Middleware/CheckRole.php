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
     * @param  string|array  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        // Check if user is authenticated
        if (!$user) {
            return redirect()->route('login')
                ->with('error', 'Please login to access this page.');
        }

        // Convert single role to array for consistent handling
        $roles = count($roles) === 1 && str_contains($roles[0], '|')
            ? explode('|', $roles[0])
            : $roles;

        // Check if user has any of the required roles
        if (!in_array($user->role, $roles)) {
            // If user is already logged in but doesn't have permission
            $redirect = match($user->role) {
                'admin' => redirect()->route('admin.dashboard'),
                'instructor' => redirect()->route('instructor.dashboard'),
                'user' => redirect()->route('client.bookings.index'),
                default => redirect()->route('home')
            };
            
            return $redirect->with('error', 'You do not have permission to access this page.');
        }

        // Add role to request for use in controllers/views
        $request->merge(['current_role' => $user->role]);

        return $next($request);
    }
}
