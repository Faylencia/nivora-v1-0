<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StoreSaleRequest;
use App\Http\Resources\SaleResource;
use App\Models\Customer;
use App\Models\Inventory;
use App\Models\MemberCoupon;
use App\Models\PointTransaction;
use App\Models\PointWallet;
use App\Models\Product;
use App\Models\Promo;
use App\Models\Sale;
use App\Models\StockMovement;
use App\Models\User;
use App\Services\MemberLoyaltyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $business = $this->business($request);
        $query = $business->sales()
            ->with(['customer', 'outlet', 'user'])
            ->when($request->filled('business_id') || $request->filled('outlet_id'), fn ($q) => $q->where('business_id', $request->integer('business_id', $request->integer('outlet_id'))))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')->toString()))
            ->when($request->filled('date'), fn ($q) => $q->whereDate('transaction_date', $request->date('date')));

        $user = $this->user($request);
        $isOwner = $user->roles()->where('roles.name', 'owner')->wherePivot('business_id', $business->id)->exists();
        $isCashier = $user->roles()->where('roles.name', 'cashier')->wherePivot('business_id', $business->id)->exists();
        if ($isCashier && ! $isOwner && ! $user->isSuperAdmin()) {
            $query->where('user_id', $user->id);
        }

        $sales = $query->latest('transaction_date')->paginate(min(100, max(1, $request->integer('limit', 20))));

        return $this->success([
            'items' => SaleResource::collection($sales->items()),
            'pagination' => [
                'page' => $sales->currentPage(),
                'limit' => $sales->perPage(),
                'total' => $sales->total(),
            ],
        ], 'Sales retrieved successfully');
    }

    public function store(StoreSaleRequest $request, MemberLoyaltyService $loyalty): JsonResponse
    {
        $business = $this->business($request);
        $data = $request->validated();
        if ((int) $data['business_id'] !== $business->id || $business->status !== 'active') {
            return response()->json(['success' => false, 'message' => 'Toko tidak sesuai dengan konteks aktif.'], 422);
        }

        $customer = null;
        if (! empty($data['customer_id'])) {
            $customer = Customer::query()->whereKey($data['customer_id'])->where('business_id', $business->id)->first();
            if (! $customer) {
                return response()->json(['success' => false, 'message' => 'Customer does not belong to this business.'], 422);
            }
        }

        $member = null;
        if (! empty($data['member_phone'])) {
            $memberPhone = preg_replace('/\D+/', '', $data['member_phone']);
            $member = User::query()->where('phone', $memberPhone)
                ->whereHas('roles', fn ($query) => $query->where('roles.name', 'member'))
                ->first();

            if (! $member) {
                return response()->json(['success' => false, 'message' => 'Nomor telepon tidak ditemukan sebagai akun member.'], 422);
            }
        }

        $sale = DB::transaction(function () use ($business, $data, $customer, $member, $request, $loyalty): Sale {
            if ($member) {
                $member = User::query()->lockForUpdate()->findOrFail($member->id);
                if ($customer && $customer->member_user_id !== null && $customer->member_user_id !== $member->id) {
                    abort(422, 'Customer yang dipilih tidak cocok dengan nomor member.');
                }

                $customer ??= Customer::query()->firstOrCreate(
                    ['business_id' => $business->id, 'member_user_id' => $member->id],
                    ['name' => $member->name, 'phone' => $member->phone, 'email' => $member->email, 'points' => 0],
                );
                if ($customer->member_user_id === null) {
                    $customer->update(['member_user_id' => $member->id]);
                }
            }

            $subtotal = 0.0;
            $preparedItems = [];

            foreach ($data['items'] as $item) {
                $product = Product::query()
                    ->whereKey($item['product_id'])
                    ->where('business_id', $business->id)
                    ->where('is_active', true)
                    ->first();

                if (! $product) {
                    abort(422, "Product {$item['product_id']} is not available.");
                }

                $inventory = Inventory::query()
                    ->where('product_id', $product->id)
                    ->where('business_id', $business->id)
                    ->lockForUpdate()
                    ->first();

                if (! $inventory || $inventory->stock - $inventory->reserved_stock < (int) $item['quantity']) {
                    abort(422, "Insufficient stock for product {$product->name}.");
                }

                $lineSubtotal = round((float) $product->selling_price * (int) $item['quantity'], 2);
                $subtotal += $lineSubtotal;
                $preparedItems[] = [
                    'product' => $product,
                    'inventory' => $inventory,
                    'quantity' => (int) $item['quantity'],
                    'subtotal' => $lineSubtotal,
                ];
            }

            $manualDiscount = max(0, (float) ($data['discount'] ?? 0));
            $promoDiscount = 0.0;
            $appliedPromos = [];
            $appliedPromoAmounts = [];
            $codedPromoId = null;

            $promoIds = array_values(array_unique($data['promo_ids'] ?? []));
            $couponCode = strtoupper(trim((string) ($data['coupon_code'] ?? '')));
            $memberCoupon = null;
            if ($couponCode !== '') {
                if (! $member) {
                    abort(422, 'Verifikasi nomor member sebelum menggunakan kode kupon.');
                }

                $memberCoupon = MemberCoupon::query()->where('code', $couponCode)->lockForUpdate()->first();

                if ($memberCoupon) {
                    if (! $member || $memberCoupon->user_id !== $member->id || $memberCoupon->business_id !== $business->id) {
                        abort(422, 'Kupon ini hanya dapat digunakan oleh member pemiliknya di toko penerbit.');
                    }
                    if ($memberCoupon->status !== 'active' || $memberCoupon->expires_at->isPast()) {
                        abort(422, 'Kupon member sudah digunakan atau kedaluwarsa.');
                    }
                    if ($subtotal < (float) $memberCoupon->min_transaction) {
                        abort(422, 'Belanja belum memenuhi minimum transaksi kupon.');
                    }
                } else {
                    $codedPromo = Promo::query()->where('business_id', $business->id)->where('code', $couponCode)->first();
                    if (! $codedPromo) {
                        abort(422, 'Kode kupon tidak ditemukan di toko ini.');
                    }
                    if (! $codedPromo->isCurrentlyActive()) {
                        abort(422, 'Kode promo tidak aktif atau sudah di luar periode.');
                    }
                    if ($codedPromo->min_transaction !== null && $subtotal < (float) $codedPromo->min_transaction) {
                        abort(422, 'Belanja belum memenuhi minimum transaksi kode promo.');
                    }
                    $codedPromoId = $codedPromo->id;
                    $promoIds[] = $codedPromo->id;
                    $promoIds = array_values(array_unique($promoIds));
                }
            }

            if ($promoIds !== []) {
                $promos = Promo::query()
                    ->where('business_id', $business->id)
                    ->whereIn('id', $promoIds)
                    ->with('products:id')
                    ->lockForUpdate()
                    ->get();

                if ($promos->count() !== count($promoIds)) {
                    abort(422, 'One or more promos do not belong to this business or do not exist.');
                }

                foreach ($promos as $promo) {
                    if (! $promo->isCurrentlyActive()) {
                        abort(422, "Promo {$promo->name} is not active in the selected period.");
                    }

                    if ($promo->type === 'product') {
                        $eligibleIds = $promo->products->pluck('id')->map(fn ($id) => (int) $id)->all();
                        $appliedPromoAmount = 0.0;
                        foreach ($preparedItems as $index => $prepared) {
                            if (! in_array((int) $prepared['product']->id, $eligibleIds, true)) {
                                continue;
                            }

                            $remainingLine = max(0, $prepared['subtotal'] - (float) ($preparedItems[$index]['discount'] ?? 0));
                            if ($remainingLine <= 0) {
                                continue;
                            }

                            $lineDiscount = round($remainingLine * ((float) $promo->discount_value / 100), 2);
                            $lineDiscount = min($remainingLine, $lineDiscount);
                            $preparedItems[$index]['discount'] = round((float) ($preparedItems[$index]['discount'] ?? 0) + $lineDiscount, 2);
                            $appliedPromoAmount += $lineDiscount;
                        }
                        if ($appliedPromoAmount > 0) {
                            $appliedPromoAmounts[$promo->id] = round($appliedPromoAmount, 2);
                        }
                    } else {
                        if ($promo->min_transaction !== null && $subtotal < (float) $promo->min_transaction) {
                            continue;
                        }

                        $remaining = max(0, $subtotal - array_sum(array_map(fn ($item) => (float) ($item['discount'] ?? 0), $preparedItems)) - $promoDiscount);
                        $transactionDiscount = round($remaining * ((float) $promo->discount_value / 100), 2);
                        $appliedAmount = min($remaining, $transactionDiscount);
                        $promoDiscount += $appliedAmount;
                        $appliedPromoAmounts[$promo->id] = round($appliedAmount, 2);
                    }

                    if (($appliedPromoAmounts[$promo->id] ?? 0) > 0) {
                        $appliedPromos[] = $promo;
                    }
                }
            }

            if ($codedPromoId !== null && ! isset($appliedPromoAmounts[$codedPromoId])) {
                abort(422, 'Promo tidak berlaku untuk produk dalam keranjang ini.');
            }

            $itemPromoDiscount = array_sum(array_map(fn ($item) => (float) ($item['discount'] ?? 0), $preparedItems));
            $memberCouponDiscount = 0.0;
            if ($memberCoupon) {
                $remainingSubtotal = max(0, $subtotal - $itemPromoDiscount - $promoDiscount - $manualDiscount);
                $memberCouponDiscount = min(
                    $remainingSubtotal,
                    round($remainingSubtotal * ((float) $memberCoupon->discount_value / 100), 2),
                );
            }
            $discount = min($subtotal, $manualDiscount + $itemPromoDiscount + $promoDiscount + $memberCouponDiscount);
            $tax = max(0, (float) ($data['tax'] ?? 0));
            $total = round(max(0, $subtotal - $discount + $tax), 2);
            $paid = round((float) $data['paid'], 2);
            $pointsToMember = (int) ($data['points_to_member'] ?? 0);
            $requestedPointsToRedeem = (int) ($data['points_to_redeem'] ?? 0);

            if ($requestedPointsToRedeem > 0 && ! $member) {
                abort(422, 'Nomor member harus diverifikasi sebelum poin digunakan untuk pembayaran.');
            }

            $pointsToRedeem = min($requestedPointsToRedeem, (int) floor($total));

            $cashDue = round(max(0, $total - $pointsToRedeem), 2);
            if ($paid < $cashDue) {
                abort(422, 'Pembayaran tunai dan poin belum mencukupi total transaksi.');
            }

            $totalChange = round($paid - $cashDue, 2);
            if ($pointsToMember > 0) {
                if (! $member) {
                    abort(422, 'Nomor member harus diverifikasi sebelum poin kembalian dikirim.');
                }
                if ($data['payment_method'] !== 'cash') {
                    abort(422, 'Poin kembalian hanya dapat digunakan pada pembayaran tunai.');
                }
                if ($pointsToMember > $totalChange) {
                    abort(422, 'Poin yang dikirim tidak boleh melebihi jumlah kembalian.');
                }
            }

            $datePrefix = now()->format('Ymd');
            $sequence = $business->sales()->whereDate('transaction_date', now()->toDateString())->lockForUpdate()->count() + 1;
            $invoiceNumber = sprintf('INV-%s-%04d', $datePrefix, $sequence);

            $sale = $business->sales()->create([
                'user_id' => $request->user()->id,
                'customer_id' => $customer?->id,
                'member_user_id' => $member?->id,
                'invoice_number' => $invoiceNumber,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'tax' => $tax,
                'total' => $total,
                'paid' => $paid,
                'change_amount' => round($totalChange - $pointsToMember, 2),
                'points_to_member' => $pointsToMember,
                'points_redeemed' => $pointsToRedeem,
                'payment_method' => $data['payment_method'],
                'status' => 'completed',
                'transaction_date' => now(),
            ]);

            foreach ($preparedItems as $prepared) {
                $sale->items()->create([
                    'product_id' => $prepared['product']->id,
                    'quantity' => $prepared['quantity'],
                    'price' => $prepared['product']->selling_price,
                    'discount' => round((float) ($prepared['discount'] ?? 0), 2),
                    'subtotal' => round($prepared['subtotal'] - (float) ($prepared['discount'] ?? 0), 2),
                ]);

                $before = $prepared['inventory']->stock;
                $after = $before - $prepared['quantity'];
                $prepared['inventory']->update(['stock' => $after]);

                StockMovement::create([
                    'inventory_id' => $prepared['inventory']->id,
                    'user_id' => $request->user()->id,
                    'type' => 'out',
                    'quantity' => -$prepared['quantity'],
                    'stock_before' => $before,
                    'stock_after' => $after,
                    'notes' => "Sale {$invoiceNumber}",
                ]);
            }

            $sale->payments()->create([
                'payment_reference' => $invoiceNumber,
                'method' => $data['payment_method'],
                'amount' => $paid,
                'status' => 'paid',
                'paid_at' => now(),
            ]);

            if ($pointsToRedeem > 0) {
                $sale->payments()->create([
                    'payment_reference' => "{$invoiceNumber}-POINTS",
                    'method' => 'points',
                    'amount' => $pointsToRedeem,
                    'status' => 'paid',
                    'paid_at' => now(),
                ]);
            }

            if ($appliedPromos !== []) {
                foreach ($appliedPromos as $promo) {
                    $sale->salePromos()->create([
                        'promo_id' => $promo->id,
                        'discount_amount' => round((float) ($appliedPromoAmounts[$promo->id] ?? 0), 2),
                    ]);
                }
            }

            if ($memberCoupon) {
                $memberCoupon->update(['status' => 'redeemed', 'redeemed_sale_id' => $sale->id]);
            }

            if ($pointsToRedeem > 0 && $member) {
                $loyalty->redeemPoints($business, $request->user(), $member, $sale, $pointsToRedeem);
            }

            if ($pointsToMember > 0 && $member) {
                $loyalty->transferChangePoints($business, $request->user(), $member, $sale, $pointsToMember);
            }

            if ($member) {
                $loyalty->issueFrequentPurchaseCoupon($business, $member, $sale);
            }

            return $sale->load(['items.product', 'payments', 'customer', 'salePromos.promo', 'business', 'memberCoupon']);
        });

        return $this->success(new SaleResource($sale), 'Sale created successfully', 201);
    }

    public function show(Request $request, Sale $sale): JsonResponse
    {
        $business = $this->business($request);
        abort_unless($sale->business_id === $business->id, 404);

        $user = $this->user($request);
        $isOwner = $user->roles()->where('roles.name', 'owner')->wherePivot('business_id', $business->id)->exists();
        if (! $isOwner && ! $user->isSuperAdmin()) {
            abort_unless($sale->user_id === $user->id, 404);
        }

        return $this->success(new SaleResource($sale->load(['items.product', 'payments', 'customer', 'outlet', 'user', 'business', 'memberCoupon'])), 'Sale retrieved successfully');
    }

    public function return(Request $request, Sale $sale, MemberLoyaltyService $loyalty): JsonResponse
    {
        $business = $this->business($request);
        abort_unless($sale->business_id === $business->id, 404);

        if ($sale->status !== 'completed') {
            return response()->json(['success' => false, 'message' => 'Only completed sales can be returned.'], 409);
        }

        DB::transaction(function () use ($sale, $request, $loyalty): void {
            $sale = Sale::query()->lockForUpdate()->findOrFail($sale->id);
            abort_unless($sale->status === 'completed', 409, 'Only completed sales can be returned.');
            $sale->load('items');

            foreach ($sale->items as $item) {
                $inventory = Inventory::query()
                    ->where('product_id', $item->product_id)
                    ->where('business_id', $sale->business_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $before = $inventory->stock;
                $after = $before + $item->quantity;
                $inventory->update(['stock' => $after]);

                StockMovement::create([
                    'inventory_id' => $inventory->id,
                    'user_id' => $request->user()->id,
                    'type' => 'in',
                    'quantity' => $item->quantity,
                    'stock_before' => $before,
                    'stock_after' => $after,
                    'notes' => "Return {$sale->invoice_number}",
                ]);

                if ($before <= 0 && $after > 0) {
                    $product = Product::query()->find($item->product_id);
                    $loyalty->notifyMembers(
                        $sale->business,
                        'stock_available',
                        "Stok {$product->name} tersedia kembali",
                        "{$product->name} di {$sale->business->locationName()}, sudah tersedia. Stok saat ini {$after} {$product->unit}.",
                        ['product_id' => $product->id, 'business_id' => $sale->business_id, 'stock' => $after],
                    );
                }
            }

            if ($sale->points_to_member > 0 && $sale->member_user_id) {
                $memberWallet = PointWallet::query()->where('wallet_key', "member:{$sale->member_user_id}")->lockForUpdate()->first();
                $cashierWallet = PointWallet::query()->where('wallet_key', "cashier:{$sale->business_id}:{$sale->user_id}")->lockForUpdate()->first();

                if (! $memberWallet || $memberWallet->balance < $sale->points_to_member || ! $cashierWallet) {
                    abort(409, 'Pengembalian ditahan: poin dari transaksi sudah digunakan atau saldo kasir tidak tersedia.');
                }

                $memberWallet->decrement('balance', $sale->points_to_member);
                $cashierWallet->increment('balance', $sale->points_to_member);
                $memberWallet->refresh();
                $cashierWallet->refresh();
                PointTransaction::query()->create([
                    'business_id' => $sale->business_id,
                    'from_wallet_id' => $memberWallet->id,
                    'to_wallet_id' => $cashierWallet->id,
                    'actor_user_id' => $request->user()->id,
                    'sale_id' => $sale->id,
                    'type' => 'change_reversal',
                    'points' => $sale->points_to_member,
                    'from_balance_after' => $memberWallet->balance,
                    'to_balance_after' => $cashierWallet->balance,
                    'description' => "Pembatalan poin transaksi {$sale->invoice_number}.",
                ]);
                $loyalty->notifyOne(
                    $sale->member,
                    $sale->business,
                    'points_reversed',
                    'Poin transaksi dibatalkan',
                    "{$sale->points_to_member} poin dari transaksi {$sale->invoice_number} ditarik kembali karena transaksi dibatalkan.",
                    ['sale_id' => $sale->id, 'points' => $sale->points_to_member],
                );
            }

            if ($sale->points_redeemed > 0 && $sale->member_user_id) {
                $loyalty->refundRedeemedPoints($sale->business, $request->user(), $sale->member, $sale, $sale->points_redeemed);
            }

            $sale->memberCoupon?->update([
                'status' => $sale->memberCoupon->expires_at->isPast() ? 'expired' : 'active',
                'redeemed_sale_id' => null,
            ]);

            MemberCoupon::query()->where('earned_at_sale_id', $sale->id)->where('status', 'active')->update(['status' => 'expired']);

            $sale->update(['status' => 'returned']);
            $sale->payments()->where('status', 'paid')->update(['status' => 'refunded']);
        });

        return $this->success(new SaleResource($sale->fresh()->load(['items.product', 'payments', 'memberCoupon'])), 'Sale returned successfully');
    }
}
