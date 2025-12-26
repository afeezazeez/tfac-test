<?php

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;

test('guests cannot checkout', function () {
    $response = $this->post(route('checkout.store'));

    $response->assertRedirect(route('login'));
});

test('authenticated users can checkout', function () {
    $user = User::factory()->create();
    $cart = Cart::factory()->create(['user_id' => $user->id]);
    $product = Product::factory()->create(['price' => 10.00, 'stock_quantity' => 10]);
    CartItem::factory()->create([
        'cart_id' => $cart->id,
        'product_id' => $product->id,
        'quantity' => 2,
    ]);

    $response = $this->actingAs($user)->post(route('checkout.store'));

    $response->assertRedirect()
        ->assertSessionHas('success', 'Order is being processed');

    $this->assertDatabaseHas('orders', [
        'user_id' => $user->id,
        'status' => 'completed',
    ]);

    expect(CartItem::where('cart_id', $cart->id)->count())->toBe(0);
});

test('checkout fails when cart is empty', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('checkout.store'));

    $response->assertRedirect()
        ->assertSessionHas('error');
});

