<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StoreCashierRequest;
use App\Http\Resources\UserResource;
use App\Models\PointTransaction;
use App\Models\PointWallet;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CashierController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $business = $this->business($request);
        $cashiers = User::query()
            ->whereHas('roles', fn (Builder $query) => $query->where('roles.name', 'cashier')->where('user_roles.business_id', $business->id))
            ->with(['roles' => fn ($query) => $query->where('roles.name', 'cashier')->where('user_roles.business_id', $business->id)])
            ->orderBy('name')
            ->paginate(min(100, max(1, $request->integer('limit', 20))));

        $items = $cashiers->getCollection()->map(function (User $cashier) use ($business, $request): array {
            $walletBalance = PointWallet::query()->where('wallet_key', "cashier:{$business->id}:{$cashier->id}")->value('balance') ?? 0;

            return [
                ...(new UserResource($cashier))->toArray($request),
                'point_balance' => (int) $walletBalance,
                'outlet_name' => $business->locationName(),
            ];
        });

        return $this->success([
            'items' => $items,
            'pagination' => [
                'page' => $cashiers->currentPage(),
                'limit' => $cashiers->perPage(),
                'total' => $cashiers->total(),
            ],
        ], 'Daftar kasir berhasil diambil.');
    }

    public function store(StoreCashierRequest $request): JsonResponse
    {
        $business = $this->business($request);
        $data = $request->validated();
        abort_unless(! isset($data['business_id']) || (int) $data['business_id'] === $business->id, 422, 'Toko tidak sesuai dengan konteks aktif.');

        $data['phone'] = preg_replace('/\D+/', '', $data['phone']);
        abort_if(User::query()->where('phone', $data['phone'])->exists(), 422, 'Nomor telepon sudah terdaftar.');

        [$cashier, $wallet] = DB::transaction(function () use ($business, $data): array {
            $cashier = User::query()->create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'password' => $data['password'],
                'status' => 'active',
            ]);
            $role = Role::query()->firstOrCreate(['name' => 'cashier'], ['description' => 'Point-of-sale cashier.']);
            $cashier->roles()->attach($role->id, ['business_id' => $business->id]);
            $wallet = PointWallet::query()->create([
                'user_id' => $cashier->id,
                'business_id' => $business->id,
                'wallet_type' => 'cashier',
                'wallet_key' => "cashier:{$business->id}:{$cashier->id}",
                'balance' => 0,
            ]);

            return [$cashier->load('roles'), $wallet];
        });

        return $this->success([
            ...(new UserResource($cashier))->toArray($request),
            'point_balance' => $wallet->balance,
        ], 'Akun kasir berhasil dibuat.', 201);
    }

    public function update(Request $request, User $cashier): JsonResponse
    {
        $business = $this->business($request);
        if ($request->filled('phone')) {
            $request->merge(['phone' => preg_replace('/\D+/', '', (string) $request->input('phone'))]);
        }
        if (! $request->filled('business_id') && $request->filled('outlet_id')) {
            $request->merge(['business_id' => $request->input('outlet_id')]);
        }
        $data = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:100'],
            'phone' => ['sometimes', 'required', 'string', 'max:30', Rule::unique('users', 'phone')->ignore($cashier->id)],
            'business_id' => ['sometimes', 'required', 'integer', 'exists:businesses,id'],
        ]);
        $assignment = $cashier->roles()->where('roles.name', 'cashier')->wherePivot('business_id', $business->id)->first();
        abort_unless($assignment, 404);

        if (isset($data['business_id'])) {
            abort_unless((int) $data['business_id'] === $business->id && $business->status === 'active', 422, 'Toko tidak sesuai dengan konteks aktif.');
        }

        if (isset($data['name'])) {
            $cashier->update(['name' => $data['name']]);
        }

        if (isset($data['phone'])) {
            $cashier->update(['phone' => preg_replace('/\D+/', '', $data['phone'])]);
        }

        return $this->success(new UserResource($cashier->fresh()->load('roles')), 'Data kasir diperbarui.');
    }

    public function destroy(Request $request, User $cashier): JsonResponse
    {
        $business = $this->business($request);
        $assignment = $cashier->roles()->where('roles.name', 'cashier')->wherePivot('business_id', $business->id)->first();
        abort_unless($assignment, 404);

        $cashier->roles()->wherePivot('business_id', $business->id)->detach($assignment->id);
        if ($cashier->roles()->count() === 0) {
            $cashier->update(['status' => 'inactive']);
        }

        return $this->success(null, 'Akses kasir untuk bisnis ini dicabut. Akun member lain tetap dipertahankan.');
    }

    public function fundPoints(Request $request, User $cashier): JsonResponse
    {
        $business = $this->business($request);
        $data = $request->validate(['points' => ['required', 'integer', 'min:1', 'max:1000000000']]);
        abort_unless($cashier->roles()->where('roles.name', 'cashier')->wherePivot('business_id', $business->id)->exists(), 404);

        $wallet = DB::transaction(function () use ($business, $cashier, $data, $request): PointWallet {
            $wallet = PointWallet::query()
                ->where('wallet_key', "cashier:{$business->id}:{$cashier->id}")
                ->lockForUpdate()
                ->firstOrFail();
            $wallet->increment('balance', $data['points']);
            $wallet->refresh();
            PointTransaction::query()->create([
                'business_id' => $business->id,
                'to_wallet_id' => $wallet->id,
                'actor_user_id' => $request->user()->id,
                'type' => 'funded',
                'points' => $data['points'],
                'to_balance_after' => $wallet->balance,
                'description' => 'Saldo poin kembalian diisi owner.',
            ]);

            return $wallet;
        });

        return $this->success(['cashier_id' => $cashier->id, 'balance' => $wallet->balance], 'Saldo poin kasir berhasil ditambah.');
    }

    public function lookupMember(Request $request): JsonResponse
    {
        $business = $this->business($request);
        $phone = preg_replace('/\D+/', '', (string) $request->query('phone', ''));
        abort_if(strlen($phone) < 7, 422, 'Masukkan nomor telepon member yang valid.');
        $member = User::query()->where('phone', $phone)
            ->whereHas('roles', fn (Builder $query) => $query->where('roles.name', 'member'))
            ->first();

        if (! $member) {
            return $this->success(['found' => false], 'Nomor ini belum terdaftar sebagai member.');
        }

        $wallet = PointWallet::query()->firstOrCreate(
            ['wallet_key' => "member:{$member->id}"],
            ['user_id' => $member->id, 'business_id' => null, 'wallet_type' => 'member', 'balance' => 0],
        );

        return $this->success([
            'found' => true,
            'member' => ['id' => $member->id, 'name' => $member->name, 'phone' => $member->phone, 'points' => $wallet->balance],
            'business_id' => $business->id,
        ], 'Member ditemukan.');
    }
}
