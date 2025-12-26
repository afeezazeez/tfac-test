<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function __construct(
        protected Order $order,
        protected OrderItem $orderItem,
        protected Product $product
    ) {
    }

    /**
     * Create order from cart items.
     */
    public function createOrderFromCart(int $userId, array $cartItems): Order
    {
        return DB::transaction(function () use ($userId, $cartItems) {
        
            $total = collect($cartItems)->sum(fn ($item) => $item->product->price * $item->quantity);

            $order = $this->order->create([
                'order_id' => 'ORD-' . strtoupper(uniqid()),
                'user_id' => $userId,
                'total' => $total,
                'status' => 'completed'
            ]);

            foreach ($cartItems as $cartItem) {
                $this->orderItem->create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->product->price,
                ]);

                // Decrement stock
                $cartItem->product->decrement('stock_quantity', $cartItem->quantity);
            }

            return $order->load('items.product');
        });
    }
}

