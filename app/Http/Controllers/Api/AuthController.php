<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\BusinessResource;
use App\Http\Resources\UserResource;
use App\Models\Business;
use App\Models\Role;
use App\Models\User;
use App\Services\ApiTokenService;
use App\Services\BusinessContext;
use App\Services\PlatformActivityLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends ApiController
{
    public function __construct(
        BusinessContext $businessContext,
        private readonly ApiTokenService $tokens,
        PlatformActivityLogger $activityLogger,
    ) {
        parent::__construct($businessContext, $activityLogger);
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $data = $request->validated();

        $user = DB::transaction(function () use ($data): User {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'phone' => $data['phone'],
                'status' => 'active',
            ]);

            $memberRole = Role::query()->firstOrCreate(
                ['name' => 'member'],
                ['description' => 'Nivora member account.'],
            );

            $user->roles()->attach($memberRole->id, ['business_id' => null]);

            return $user;
        });

        $token = $this->tokens->issue($user);
        $user->load('roles');

        return response()->json([
            'success' => true,
            'message' => 'Registration successful',
            'data' => [
                'token' => $token['token'],
                'expires_at' => $token['expires_at'],
                'user' => new UserResource($user),
                'business' => null,
            ],
        ], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = User::query()->where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password) || $user->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials.',
            ], 401);
        }

        $user->load('roles');

        $assignment = $user->isSuperAdmin() ? null : DB::table('user_roles')
            ->join('businesses', 'businesses.id', '=', 'user_roles.business_id')
            ->join('roles', 'roles.id', '=', 'user_roles.role_id')
            ->where('user_roles.user_id', $user->id)
            ->whereIn('roles.name', ['owner', 'cashier'])
            ->where('businesses.status', 'active')
            ->select(['user_roles.business_id'])
            ->orderByRaw("CASE roles.name WHEN 'owner' THEN 0 ELSE 1 END")
            ->orderBy('user_roles.id')
            ->first();

        if (! $assignment && ! $user->isSuperAdmin() && ! $user->hasRole('member')) {
            return response()->json([
                'success' => false,
                'message' => 'User does not have an active role.',
            ], 403);
        }

        $token = $this->tokens->issue($user, $assignment ? (int) $assignment->business_id : null);
        $business = null;
        if ($assignment) {
            $businessQuery = Business::query();
            $business = $businessQuery->findOrFail($assignment->business_id);
        }

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'token' => $token['token'],
                'expires_at' => $token['expires_at'],
                'user' => new UserResource($user),
                'business' => $business ? new BusinessResource($business) : null,
            ],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $token = $request->attributes->get('api.token');
        $payload = $request->attributes->get('api.token_payload');

        if (is_string($token) && is_array($payload) && isset($payload['exp'])) {
            $this->tokens->revoke($token, (int) $payload['exp']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Logout successful',
            'data' => null,
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $this->user($request)->load('roles');

        return $this->success(new UserResource($user), 'Authenticated user');
    }
}
