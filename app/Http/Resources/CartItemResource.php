<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'quantity' => $this->quantity,
            'product' => [
                'id' => $this->product->id,
                'name' => $this->product->name,
                'price' => (float) $this->product->price,
                'formatted_price' => '$' . number_format($this->product->price, 2),
                'stock_quantity' => $this->product->stock_quantity,
            ],
            'subtotal' => (float) ($this->product->price * $this->quantity),
            'formatted_subtotal' => '$' . number_format($this->product->price * $this->quantity, 2),
        ];
    }
}
