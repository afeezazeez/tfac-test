<?php

namespace App\Observers;

use App\Jobs\LowStockNotificationJob;
use App\Models\Product;
use Illuminate\Support\Facades\Log;

class ProductObserver
{
    /**
     * Handle the Product "updated" event.
     */
    public function updated(Product $product): void
    {
        if (! $product->wasChanged('stock_quantity')) {
            return;
        }

        $threshold = config('app.low_stock_threshold', 5);
        $oldStock = $product->getOriginal('stock_quantity');
        $newStock = $product->stock_quantity;


        if ($newStock <= $threshold && $oldStock > $threshold) {

        Log::info('Product stock updated', [
            'product_id' => $product->id,
            'product_name' => $product->name,
            'old_stock' => $oldStock,
            'new_stock' => $newStock,
            'threshold' => $threshold,
        ]);
        LowStockNotificationJob::dispatch($product);
        }
    }
}
