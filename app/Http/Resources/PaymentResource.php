<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id '            => $this->id,
            'amount'         => $this->amount,
            'gateway'        => $this->gateway,
            'transaction_id' => $this->transaction_id,
            'status'         => $this->status,
            'approve_url'    => $this->approve_url,
            'metadata'       => $this->metadata,
            'paid_at'        => $this->created_at->toDateTimeString(),
        ];
    }
}
