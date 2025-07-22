<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DatabaseManagerMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user has database manager role
        // For now, allow all authenticated users (you can implement proper role checking later)
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // TODO: Implement proper role checking when user roles are set up
        // For now, we'll allow all authenticated users to access database management
        // This should be restricted to specific users in production
        
        return $next($request);
    }
}