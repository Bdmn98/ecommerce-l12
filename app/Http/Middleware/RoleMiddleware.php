<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request and ensure user has correct role.
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();
        if (!$user || ($user->role instanceof \App\Enums\UserRoleEnum
                ? $user->role->value !== $role
                : $user->role !== $role)) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        return $next($request);
    }
}
