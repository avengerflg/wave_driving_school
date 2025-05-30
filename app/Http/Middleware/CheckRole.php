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
        if (in_array($user->role, $roles)) {
            // User has the required role, continue
            $request->merge(['current_role' => $user->role]);
            return $next($request);
        }

        // User doesn't have the required role, redirect to their appropriate dashboard
        // BUT only if they're not already trying to access their own dashboard
        $currentRoute = $request->route()->getName();
        
        switch($user->role) {
            case 'admin':
                if (!str_starts_with($currentRoute, 'admin.')) {
                    return redirect()->route('admin.dashboard')
                        ->with('error', 'You do not have permission to access this page.');
                }
                break;
            case 'instructor':
                if (!str_starts_with($currentRoute, 'instructor.')) {
                    return redirect()->route('instructor.dashboard')
                        ->with('error', 'You do not have permission to access this page.');
                }
                break;
            case 'student':
                if (!str_starts_with($currentRoute, 'client.')) {
                    return redirect()->route('client.dashboard')
                        ->with('error', 'You do not have permission to access this page.');
                }
                break;
            default:
                return redirect()->route('home')
                    ->with('error', 'Invalid user role.');
        }

        // If we reach here, user is trying to access their own dashboard but doesn't have permission
        // This shouldn't happen in normal circumstances
        abort(403, 'Access denied');
    }
}
