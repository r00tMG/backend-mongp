<?php

namespace App\Http\Resources\User;

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
            'id' => $this->resource->id,
            'payment_intent_id' => $this->resource->payment_intent_id,
            'demande' => new DemandeResource($this->resource->demande),
            'total' => $this->resource->total,
            'status' => $this->resource->status,
            'payment_status' => $this->resource->payment_status,
            'paid_at' => $this->resource->paid_at,
        ];
    }
}
