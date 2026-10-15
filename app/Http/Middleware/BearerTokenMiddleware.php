<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Services\ApiTokenService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BearerTokenMiddleware
{
    public function __construct(private readonly ApiTokenService $tokens) {}

    public function handle(Request $request, Closure $next): Response
    {
        $header = $request->header('Authorization');

        if (! $header || ! str_starts_with($header, 'Bearer ')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated. Bearer token is required.',
            ], 401);
        }

        $token = trim(substr($header, 7));
        $payload = $this->tokens->parse($token);
        $user = $payload ? User::find($payload['sub']) : null;

        if (! $user || $user->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated. Token is invalid or expired.',
            ], 401);
        }

        $request->setUserResolver(fn (): User => $user);
        $request->attributes->set('api.token', $token);
        $request->attributes->set('api.token_payload', $payload);

        return $next($request);
    }
}
