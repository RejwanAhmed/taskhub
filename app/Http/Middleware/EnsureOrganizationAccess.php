<?php

namespace App\Http\Middleware;

use App\Support\OrganizationSession;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureOrganizationAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        $currentOrganizationId = OrganizationSession::getCurrentOrg();

        if (!$currentOrganizationId) {
            abort(403, 'No active organization.');
        }

        $belongs = $user->organizations()->where('organization_id', $currentOrganizationId)->exists();

        if (!$belongs) {
            abort(403, 'Access denied.');
        }
        
        return $next($request);
    }
}
