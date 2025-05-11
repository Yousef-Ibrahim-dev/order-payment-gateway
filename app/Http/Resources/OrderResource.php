<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'uuid'              => $this->uuid,
            'order_number'      => $this->order_number,
            'status'            => $this->status,
            'sub_total'         => $this->sub_total,
            'tax'               => $this->tax,
            'discount'          => $this->discount,
            'currency'          => $this->currency,
            'total'             => $this->total,
            'shipping_address'  => $this->shipping_address,
            'billing_address'   => $this->billing_address,
            'items'             => OrderItemResource::collection($this->whenLoaded('items')),
            'payments'          => PaymentResource::collection($this->whenLoaded('payments')),
            'created_at'        => $this->created_at->toDateTimeString(),
            'updated_at'        => $this->updated_at->toDateTimeString(),
        ];
    }
}
