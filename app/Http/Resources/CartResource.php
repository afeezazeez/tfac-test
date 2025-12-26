<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $items = $this->items ?? collect();
        $total = $items->sum(fn ($item) => $item->product->price * $item->quantity);
        $itemCount = $items->sum('quantity');

        return [
            'id' => $this->id,
            'items' => $items->map(fn ($item) => CartItemResource::make($item)->resolve($request))->values()->all(),
            'item_count' => $itemCount,
            'total' => (float) $total,
            'formatted_total' => '$' . number_format($total, 2),
        ];
    }
}
