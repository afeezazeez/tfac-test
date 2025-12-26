<?php

namespace App\Services;

use App\Exceptions\ClientErrorException;
use App\Models\Order;

class CheckoutService
{
    public function __construct(
        protected CartService $cartService,
        protected OrderService $orderService
    ) {
    }

    /**
     * Process checkout: create order and clear cart.
     */
    public function processCheckout(int $userId): Order
    {
        $cart = $this->cartService->getCartWithItems($userId);

        if (! $cart || $cart->items->isEmpty()) {
            throw new ClientErrorException('Cart is empty');
        }

        $order = $this->orderService->createOrderFromCart($userId, $cart->items->all());

        $this->cartService->clearCart($userId);

        return $order;
    }
}

