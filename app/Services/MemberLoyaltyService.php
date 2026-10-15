<?php

namespace App\Services;

use App\Models\Business;
use App\Models\MemberCoupon;
use App\Models\MemberNotification;
use App\Models\PointTransaction;
use App\Models\PointWallet;
use App\Models\Promo;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class MemberLoyaltyService
{
    public function memberWallet(User $member): PointWallet
    {
        return PointWallet::query()->firstOrCreate(
            ['wallet_key' => "member:{$member->id}"],
            ['user_id' => $member->id, 'business_id' => null, 'wallet_type' => 'member', 'balance' => 0],
        );
    }

    public function cashierWallet(User $cashier, Business $business): PointWallet
    {
        return PointWallet::query()->firstOrCreate(
            ['wallet_key' => "cashier:{$business->id}:{$cashier->id}"],
            ['user_id' => $cashier->id, 'business_id' => $business->id, 'wallet_type' => 'cashier', 'balance' => 0],
        );
    }

    public function transferChangePoints(Business $business, User $cashier, User $member, Sale $sale, int $points): void
    {
        if ($points < 1) {
            return;
        }

        $cashierWallet = PointWallet::query()
            ->where('wallet_key', "cashier:{$business->id}:{$cashier->id}")
            ->lockForUpdate()
            ->first();

        if (! $cashierWallet || $cashierWallet->balance < $points) {
            throw ValidationException::withMessages(['points_to_member' => 'Saldo poin kasir tidak mencukupi. Minta owner mengisi saldo poin.']);
        }

        $memberWallet = $this->memberWallet($member);
        $memberWallet = PointWallet::query()->lockForUpdate()->findOrFail($memberWallet->id);
        $cashierWallet->decrement('balance', $points);
        $memberWallet->increment('balance', $points);
        $cashierWallet->refresh();
        $memberWallet->refresh();

        PointTransaction::query()->create([
            'business_id' => $business->id,
            'from_wallet_id' => $cashierWallet->id,
            'to_wallet_id' => $memberWallet->id,
            'actor_user_id' => $cashier->id,
            'sale_id' => $sale->id,
            'type' => 'change_transfer',
            'points' => $points,
            'from_balance_after' => $cashierWallet->balance,
            'to_balance_after' => $memberWallet->balance,
            'description' => "Poin pengganti kembalian untuk {$sale->invoice_number}.",
        ]);

        $this->notifyOne($member, $business, 'points_received', 'Poin kembalian diterima', "{$points} poin dari transaksi {$business->name} berhasil masuk ke saldo Anda.", [
            'sale_id' => $sale->id,
            'points' => $points,
        ]);
    }

    public function redeemPoints(Business $business, User $cashier, User $member, Sale $sale, int $points): void
    {
        if ($points < 1) {
            return;
        }

        $memberWallet = $this->memberWallet($member);
        $memberWallet = PointWallet::query()->lockForUpdate()->findOrFail($memberWallet->id);
        if ($memberWallet->balance < $points) {
            throw ValidationException::withMessages(['points_to_redeem' => 'Saldo poin member tidak mencukupi.']);
        }

        $memberWallet->decrement('balance', $points);
        $memberWallet->refresh();

        PointTransaction::query()->create([
            'business_id' => $business->id,
            'from_wallet_id' => $memberWallet->id,
            'to_wallet_id' => null,
            'actor_user_id' => $cashier->id,
            'sale_id' => $sale->id,
            'type' => 'redemption',
            'points' => $points,
            'from_balance_after' => $memberWallet->balance,
            'to_balance_after' => null,
            'description' => "Poin digunakan untuk pembayaran transaksi {$sale->invoice_number}.",
        ]);

        $this->notifyOne($member, $business, 'points_spent', 'Poin digunakan', "{$points} poin digunakan untuk membayar transaksi {$sale->invoice_number} di {$business->name}.", [
            'sale_id' => $sale->id,
            'points' => $points,
        ]);
    }

    public function refundRedeemedPoints(Business $business, User $actor, User $member, Sale $sale, int $points): void
    {
        if ($points < 1) {
            return;
        }

        $memberWallet = $this->memberWallet($member);
        $memberWallet = PointWallet::query()->lockForUpdate()->findOrFail($memberWallet->id);
        $memberWallet->increment('balance', $points);
        $memberWallet->refresh();

        PointTransaction::query()->create([
            'business_id' => $business->id,
            'from_wallet_id' => null,
            'to_wallet_id' => $memberWallet->id,
            'actor_user_id' => $actor->id,
            'sale_id' => $sale->id,
            'type' => 'redemption_refund',
            'points' => $points,
            'from_balance_after' => null,
            'to_balance_after' => $memberWallet->balance,
            'description' => "Pengembalian {$points} poin dari transaksi {$sale->invoice_number}.",
        ]);

        $this->notifyOne($member, $business, 'points_refunded', 'Poin dikembalikan', "{$points} poin dari transaksi {$sale->invoice_number} telah dikembalikan ke saldo Anda.", [
            'sale_id' => $sale->id,
            'points' => $points,
        ]);
    }

    public function issueFrequentPurchaseCoupon(Business $business, User $member, Sale $sale): ?MemberCoupon
    {
        $purchaseCount = Sale::query()
            ->where('business_id', $business->id)
            ->where('member_user_id', $member->id)
            ->where('status', 'completed')
            ->count();

        if ($purchaseCount < 10 || $purchaseCount % 10 !== 0) {
            return null;
        }

        $coupon = MemberCoupon::query()->firstOrCreate(
            ['user_id' => $member->id, 'business_id' => $business->id, 'milestone' => $purchaseCount],
            [
                'code' => $this->generateCouponCode(),
                'discount_value' => 10,
                'min_transaction' => 100000,
                'status' => 'active',
                'expires_at' => now()->addDays(30),
                'earned_at_sale_id' => $sale->id,
            ],
        );

        if ($coupon->wasRecentlyCreated) {
            $this->notifyOne(
                $member,
                $business,
                'coupon_received',
                'Kupon loyalitas baru',
                "Terima kasih sudah belanja {$purchaseCount} kali di {$business->name}. Gunakan kupon {$coupon->code} untuk diskon 10% minimal Rp100.000.",
                ['coupon_id' => $coupon->id, 'code' => $coupon->code],
            );
        }

        return $coupon;
    }

    public function notifyMembers(Business $business, string $type, string $title, string $message, array $data = []): void
    {
        User::query()->whereHas('roles', fn ($query) => $query->where('roles.name', 'member'))
            ->select('users.id')
            ->chunkById(500, function ($members) use ($business, $type, $title, $message, $data): void {
                $now = now();
                $rows = $members->map(fn (User $member): array => [
                    'user_id' => $member->id,
                    'business_id' => $business->id,
                    'type' => $type,
                    'title' => $title,
                    'message' => $message,
                    'data' => $data === [] ? null : json_encode($data, JSON_THROW_ON_ERROR),
                    'created_at' => $now,
                    'updated_at' => $now,
                ])->all();

                MemberNotification::query()->insert($rows);
            });
    }

    public function notifyOne(User $member, ?Business $business, string $type, string $title, string $message, array $data = []): MemberNotification
    {
        return MemberNotification::query()->create([
            'user_id' => $member->id,
            'business_id' => $business?->id,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
        ]);
    }

    private function generateCouponCode(): string
    {
        do {
            $code = Str::upper(Str::random(10));
        } while (MemberCoupon::query()->where('code', $code)->exists() || Promo::query()->where('code', $code)->exists());

        return $code;
    }
}
