<?php

namespace App\Http\Middleware;

use App\Support\Deny;
use Closure;
use Illuminate\Http\Request;

class EnsureIsAdmin
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (! $user || ! method_exists($user, 'isAdmin') || ! $user->isAdmin()) {
            return Deny::hiddenOrForbidden();
        }

        return $next($request);
    }
}
