<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminOrModeratorMiddleware
{

    public function handle(Request $request, Closure $next): Response
    {

        // Check if the user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'You must be logged in to access this area.');
        }

        // Get the authenticated user
        $user = Auth::user();
        $user->loadMissing('grant');

        // Allow access for admins or moderators
        if ($user->grant && ($user->grant->is_admin || $user->grant->is_moderator)) {
            return $next($request);
        }

        // Otherwise, redirect with a flash message
        return redirect()
            ->route('dashboard')
            ->with('error', 'You are not authorized to access this area.');
    }
}