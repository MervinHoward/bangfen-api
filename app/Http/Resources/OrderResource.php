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
            "id" => $this->id,
            "date" => $this->date,
            "order_type" => $this->order_type,
            "total_price" => $this->total_price,
            "status" => $this->status,
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
            'payment' => new PaymentResouce($this->whenLoaded('payment'))
        ];
    }
}
