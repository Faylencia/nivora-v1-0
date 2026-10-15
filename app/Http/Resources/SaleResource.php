<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SaleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'business_id' => $this->business_id,
            'outlet_id' => $this->business_id,
            'user_id' => $this->user_id,
            'customer_id' => $this->customer_id,
            'member_user_id' => $this->member_user_id,
            'invoice_number' => $this->invoice_number,
            'subtotal' => $this->subtotal,
            'discount' => $this->discount,
            'tax' => $this->tax,
            'total' => $this->total,
            'paid' => $this->paid,
            'change' => $this->change_amount,
            'points_to_member' => $this->points_to_member,
            'points_redeemed' => $this->points_redeemed,
            'payment_method' => $this->payment_method,
            'status' => $this->status,
            'transaction_date' => $this->transaction_date,
            'outlet' => new OutletResource($this->whenLoaded('outlet')),
            'business' => new BusinessResource($this->whenLoaded('business')),
            'user' => new UserResource($this->whenLoaded('user')),
            'items' => $this->whenLoaded('items'),
            'payments' => PaymentResource::collection($this->whenLoaded('payments')),
            'customer' => new CustomerResource($this->whenLoaded('customer')),
            'promos' => PromoResource::collection($this->whenLoaded('promos')),
            'sale_promos' => $this->whenLoaded('salePromos'),
            'member_coupon' => $this->whenLoaded('memberCoupon'),
        ];
    }
}
