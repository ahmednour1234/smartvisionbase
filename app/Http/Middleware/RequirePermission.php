<?php

namespace App\Http\Middleware;

use App\Support\Deny;
use Closure;
use Illuminate\Http\Request;

class RequirePermission
{
    /**
     * Usage: ->middleware('require_perm:leads.view')
     */
    public function handle(Request $request, Closure $next, string $permission)
    {
        $user = $request->user();

        if (! $user) {
            return Deny::unauthorized();
        }

        // Use Spatie permissions when available; fall back to legacy admin check.
        $allowed = false;
        if (method_exists($user, 'can')) {
            $allowed = $user->can($permission);
        }
        if (! $allowed && method_exists($user, 'isAdmin')) {
            $allowed = $user->isAdmin();
        }

        if (! $allowed) {
            return Deny::hiddenOrForbidden();
        }

        return $next($request);
    }
}
