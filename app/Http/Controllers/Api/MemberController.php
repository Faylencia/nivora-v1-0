<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\SaleResource;
use App\Models\MemberCoupon;
use App\Models\MemberNotification;
use App\Models\PointTransaction;
use App\Models\PointWallet;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MemberController extends ApiController
{
    public function dashboard(Request $request): JsonResponse
    {
        $member = $this->user($request);
        $wallet = PointWallet::query()->where('wallet_key', "member:{$member->id}")->first();

        return $this->success([
            'member' => [
                'id' => $member->id,
                'name' => $member->name,
                'phone' => $member->phone,
                'email' => $member->email,
            ],
            'points' => $wallet?->balance ?? 0,
            'purchase_count' => $member->memberSales()->where('status', 'completed')->count(),
            'unread_notifications' => MemberNotification::query()->where('user_id', $member->id)->whereNull('read_at')->count(),
            'active_coupons' => MemberCoupon::query()->where('user_id', $member->id)->where('status', 'active')->where('expires_at', '>', now())->count(),
        ], 'Ringkasan akun member.');
    }

    public function sales(Request $request): JsonResponse
    {
        $sales = $this->user($request)->memberSales()
            ->with(['business', 'outlet', 'items.product', 'payments'])
            ->latest('transaction_date')
            ->paginate(min(100, max(1, $request->integer('limit', 20))));

        return $this->success([
            'items' => SaleResource::collection($sales->items()),
            'pagination' => ['page' => $sales->currentPage(), 'limit' => $sales->perPage(), 'total' => $sales->total()],
        ], 'Riwayat belanja member.');
    }

    public function coupons(Request $request): JsonResponse
    {
        $coupons = MemberCoupon::query()
            ->with('business:id,name')
            ->where('user_id', $this->user($request)->id)
            ->orderByDesc('created_at')
            ->paginate(min(100, max(1, $request->integer('limit', 20))));

        return $this->success([
            'items' => $coupons->items(),
            'pagination' => ['page' => $coupons->currentPage(), 'limit' => $coupons->perPage(), 'total' => $coupons->total()],
        ], 'Kupon member.');
    }

    public function wallet(Request $request): JsonResponse
    {
        $member = $this->user($request);
        $wallet = PointWallet::query()->where('wallet_key', "member:{$member->id}")->first();
        $transactions = $wallet
            ? PointTransaction::query()->with(['fromWallet.user:id,name', 'toWallet.user:id,name', 'sale:id,invoice_number'])
                ->where(fn ($query) => $query->where('from_wallet_id', $wallet->id)->orWhere('to_wallet_id', $wallet->id))
                ->latest('id')->limit(50)->get()
            : collect();

        return $this->success(['balance' => $wallet?->balance ?? 0, 'transactions' => $transactions], 'Saldo dan riwayat poin member.');
    }

    public function notifications(Request $request): JsonResponse
    {
        $memberId = $this->user($request)->id;
        $notifications = MemberNotification::query()->with('business:id,name')
            ->where('user_id', $memberId)
            ->latest()
            ->paginate(min(100, max(1, $request->integer('limit', 20))));

        return $this->success([
            'items' => $notifications->items(),
            'unread_count' => MemberNotification::query()->where('user_id', $memberId)->whereNull('read_at')->count(),
            'pagination' => ['page' => $notifications->currentPage(), 'limit' => $notifications->perPage(), 'total' => $notifications->total()],
        ], 'Notifikasi member.');
    }

    public function markNotificationRead(Request $request, MemberNotification $notification): JsonResponse
    {
        abort_unless($notification->user_id === $this->user($request)->id, 404);
        $notification->update(['read_at' => now()]);

        return $this->success($notification->fresh(), 'Notifikasi ditandai sudah dibaca.');
    }
}
