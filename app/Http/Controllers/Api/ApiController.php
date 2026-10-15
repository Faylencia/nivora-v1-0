<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\User;
use App\Services\BusinessContext;
use App\Services\PlatformActivityLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

abstract class ApiController extends Controller
{
    public function __construct(
        protected readonly BusinessContext $businessContext,
        protected readonly PlatformActivityLogger $activityLogger,
    ) {}

    protected function user(Request $request): User
    {
        /** @var User $user */
        $user = $request->user();

        return $user;
    }

    protected function business(Request $request): Business
    {
        return $this->businessContext->resolveBusiness($request, $this->user($request));
    }

    protected function success(mixed $data = null, string $message = 'Success', int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $status);
    }
}
