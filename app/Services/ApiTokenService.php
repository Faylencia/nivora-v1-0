<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class ApiTokenService
{
    private const TTL_MINUTES = 1440;

    /**
     * @return array{token: string, expires_at: int}
     */
    public function issue(User $user, ?int $businessId = null, ?int $outletId = null): array
    {
        $expiresAt = now()->addMinutes(self::TTL_MINUTES)->timestamp;
        $payload = [
            'sub' => $user->id,
            'business_id' => $businessId,
            'outlet_id' => $outletId,
            'iat' => now()->timestamp,
            'exp' => $expiresAt,
            'jti' => Str::random(32),
        ];

        return [
            'token' => Crypt::encryptString(json_encode($payload, JSON_THROW_ON_ERROR)),
            'expires_at' => $expiresAt,
        ];
    }

    /**
     * @return array{sub: int, business_id: ?int, outlet_id: ?int, iat: int, exp: int, jti: string}|null
     */
    public function parse(string $token): ?array
    {
        if ($this->isRevoked($token)) {
            return null;
        }

        try {
            $payload = json_decode(Crypt::decryptString($token), true, 512, JSON_THROW_ON_ERROR);
        } catch (DecryptException|\JsonException) {
            return null;
        }

        if (! is_array($payload)) {
            return null;
        }

        if (! isset($payload['sub'], $payload['exp'], $payload['iat'], $payload['jti'])) {
            return null;
        }

        if ((int) $payload['exp'] < now()->timestamp) {
            return null;
        }

        return [
            'sub' => (int) $payload['sub'],
            'business_id' => isset($payload['business_id']) ? (int) $payload['business_id'] : null,
            'outlet_id' => isset($payload['outlet_id']) ? (int) $payload['outlet_id'] : null,
            'iat' => (int) $payload['iat'],
            'exp' => (int) $payload['exp'],
            'jti' => (string) $payload['jti'],
        ];
    }

    public function revoke(string $token, int $expiresAt): void
    {
        $seconds = max(1, $expiresAt - now()->timestamp);
        Cache::put($this->revocationKey($token), true, $seconds);
    }

    private function isRevoked(string $token): bool
    {
        return Cache::has($this->revocationKey($token));
    }

    private function revocationKey(string $token): string
    {
        return 'nivora:revoked-token:'.hash('sha256', $token);
    }
}
