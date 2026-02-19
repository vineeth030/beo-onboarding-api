<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureEmployeeHasActiveOffer
{
    public function handle(Request $request, Closure $next): mixed
    {
        $user = $request->user();

        if (in_array($user->role, ['admin', 'superadmin'])) {
            return $next($request);
        }

        $employee = $user->employee;

        if (! $employee) {
            return $next($request);
        }

        $hasActiveOffer = $employee->offers()
            ->where('is_revoked', false)
            ->where('is_declined', false)
            ->exists();

        if (! $hasActiveOffer) {
            abort(403, 'No active offer found. Please contact the HR team.');
        }

        return $next($request);
    }
}
