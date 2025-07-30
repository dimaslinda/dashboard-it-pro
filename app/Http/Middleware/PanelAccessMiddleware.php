<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PanelAccessMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $allowedTenant): Response
    {
        // Skip check for login routes
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();
        
        // Check if user has a tenant
        if (!$user->tenant) {
            abort(403, 'User tidak memiliki tenant yang valid.');
        }

        // Check if user's tenant matches the allowed tenant for this panel
        if ($user->tenant->slug !== $allowedTenant) {
            abort(403, 'Anda tidak memiliki akses ke panel ini.');
        }

        return $next($request);
    }
}