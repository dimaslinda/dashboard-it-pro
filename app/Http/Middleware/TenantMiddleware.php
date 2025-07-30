<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TenantMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip tenant check for login and public routes
        if ($request->routeIs('filament.secret.auth.login') || !Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();
        
        // If user doesn't have a tenant, redirect to tenant selection or assign default
        if (!$user->tenant_id) {
            // For existing users, assign them to IT Dashboard tenant
            $itTenant = Tenant::where('slug', 'it-dashboard')->first();
            if ($itTenant) {
                $user->update(['tenant_id' => $itTenant->id]);
            }
        }

        // Set current tenant in session
        if ($user->tenant) {
            session(['current_tenant' => $user->tenant]);
            config(['app.current_tenant' => $user->tenant]);
        }

        return $next($request);
    }
}