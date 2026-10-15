<?php

namespace App\Services;

use App\Models\Business;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BusinessContext
{
    public function resolveBusiness(Request $request, User $user): Business
    {
        $tokenBusinessId = $request->attributes->get('api.token_payload')['business_id'] ?? null;
        $businessId = $request->header('X-Business-ID') ?? $tokenBusinessId;

        if ($businessId !== null && ! ctype_digit((string) $businessId)) {
            throw new NotFoundHttpException('Business not found.');
        }

        if ($user->isSuperAdmin()) {
            $businessQuery = Business::query()->where('status', 'active');

            if ($businessId !== null) {
                $businessQuery->whereKey((int) $businessId);
            }

            $business = $businessQuery->orderBy('id')->first();

            if (! $business) {
                throw new NotFoundHttpException('Business not found.');
            }

            return $business;
        }

        $pivotQuery = $user->businesses();

        if ($businessId !== null) {
            $pivotQuery->where('businesses.id', (int) $businessId);
        }

        $business = $pivotQuery->where('businesses.status', 'active')->first();

        if (! $business) {
            throw new AccessDeniedHttpException('You do not have access to this business.');
        }

        return $business;
    }
}
