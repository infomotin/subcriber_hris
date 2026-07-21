<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminRole
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (! $user) {
            return redirect()->route('login');
        }

        // Allow System Admin and Business Admin to access admin routes
        if ($user->hasRole('System Admin') || $user->hasRole('Business Admin')) {
            return $next($request);
        }

        // If user is Subscriber, redirect to subscriber dashboard
        if ($user->hasRole('Subscriber')) {
            return redirect()->route('subscriber.dashboard')
                ->with('error', 'Access denied. Redirected to Subscriber Dashboard.');
        }

        // If user is Demo, redirect to demo dashboard
        if ($user->hasRole('Demo User')) {
            return redirect()->route('demo.dashboard');
        }

        return redirect()->route('home');
    }
}
