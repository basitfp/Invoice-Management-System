<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
  public function handle(Request $request, Closure $next): Response
{
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    $user = Auth::user();

    if (!$user->status) {

        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('error', 'Your account has been deactivated. Please contact administrator.');
    }

    if ($user->role !== 'admin') {
        return redirect()->route('agent.dashboard')
            ->with('error', 'Unauthorized access to admin area.');
    }

    return $next($request);
}
}
