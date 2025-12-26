<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $threshold = config('app.low_stock_threshold');

        return [
            'id' => $this->id,
            'name' => $this->name,
            'price' => (float) $this->price,
            'formatted_price' => '$' . number_format($this->price, 2),
            'stock_quantity' => $this->stock_quantity,
            'is_out_of_stock' => $this->isOutOfStock(),
            'is_low_stock' => $this->isLowStock($threshold),
        ];
    }
}
