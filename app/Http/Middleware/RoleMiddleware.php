<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user || ! $user->hasRole($roles, $this->businessId($request))) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden.',
            ], 403);
        }

        return $next($request);
    }

    private function businessId(Request $request): ?int
    {
        $id = $request->header('X-Business-ID') ?? $request->attributes->get('api.token_payload')['business_id'] ?? null;

        return $id === null ? null : (int) $id;
    }
}
