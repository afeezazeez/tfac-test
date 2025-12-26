<?php

use App\Exceptions\ClientErrorException;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\CheckoutService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->checkoutService = app(CheckoutService::class);
});

test('process checkout creates order and clears cart', function () {
    $cart = Cart::factory()->create(['user_id' => $this->user->id]);
    $product = Product::factory()->create(['price' => 10.00, 'stock_quantity' => 10]);
    $cartItem = CartItem::factory()->create([
        'cart_id' => $cart->id,
        'product_id' => $product->id,
        'quantity' => 2,
    ]);

    $order = $this->checkoutService->processCheckout($this->user->id);

    expect($order)->toBeInstanceOf(Order::class)
        ->and(Order::where('user_id', $this->user->id)->count())->toBe(1)
        ->and(CartItem::where('cart_id', $cart->id)->count())->toBe(0);
});

test('process checkout throws exception when cart is empty', function () {
    expect(fn () => $this->checkoutService->processCheckout($this->user->id))
        ->toThrow(ClientErrorException::class, 'Cart is empty');
});

test('process checkout throws exception when cart has no items', function () {
    Cart::factory()->create(['user_id' => $this->user->id]);

    expect(fn () => $this->checkoutService->processCheckout($this->user->id))
        ->toThrow(ClientErrorException::class, 'Cart is empty');
});

