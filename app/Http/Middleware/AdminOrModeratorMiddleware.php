<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminOrModeratorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        // Check if the user is authenticated
        if (!Auth::check()) {
            // Debugging: What does Auth::user() return if not checked? Should be null.
            dd('Auth::check() is false. Auth::user() is: ' . (Auth::user() ? get_class(Auth::user()) : 'null'));

            // If not authenticated, redirect to login        
            return redirect()->route('login');
        }

        // Get the authenticated user
        $user = Auth::user();

        // Check if the user has a userGrant relationship loaded
        // We eager load it in the User model later for efficiency
        $user->loadMissing('grant');

        // Check if the user has a grant and if they are admin or moderator
        if ($user->grant && ($user->grant->is_admin || $user->grant->is_moderator)) {
            // If they are admin or moderator, allow the request to proceed
            return $next($request);
        }

        // If they are not authorized, abort with a 403 Forbidden error
        // Or you could redirect them to a different page, like the dashboard
        abort(403, 'Unauthorized action.');
        // Or redirect: return redirect()->route('dashboard')->with('error', 'You are not authorized to access this area.');
    }
}