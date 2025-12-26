<?php

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('guests cannot access cart', function () {
    $response = $this->get(route('cart.index'));

    $response->assertRedirect(route('login'));
});

test('authenticated users can view their cart', function () {
    $user = User::factory()->create();
    $cart = Cart::factory()->create(['user_id' => $user->id]);
    $product = Product::factory()->create();
    CartItem::factory()->create([
        'cart_id' => $cart->id,
        'product_id' => $product->id,
        'quantity' => 2,
    ]);

    $response = $this->actingAs($user)->get(route('cart.index'));

    $response->assertStatus(200);
    
    $response->assertInertia(fn (Assert $page) => $page
        ->has('cart')
    );
    
    $this->assertDatabaseHas('cart_items', [
        'cart_id' => $cart->id,
        'product_id' => $product->id,
        'quantity' => 2,
    ]);
});

test('authenticated users can add item to cart', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['stock_quantity' => 10]);

    $response = $this->actingAs($user)->post(route('cart.store'), [
        'product_id' => $product->id,
        'quantity' => 2,
    ]);

    $response->assertRedirect()
        ->assertSessionHas('success');

    $this->assertDatabaseHas('cart_items', [
        'product_id' => $product->id,
        'quantity' => 2,
    ]);
});

test('add item to cart validates product_id is required', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('cart.store'), [
        'quantity' => 1,
    ]);

    $response->assertSessionHasErrors('product_id');
});

test('add item to cart uses default quantity when not provided', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['stock_quantity' => 10]);

    $response = $this->actingAs($user)->post(route('cart.store'), [
        'product_id' => $product->id,
    ]);

    $response->assertRedirect()
        ->assertSessionHas('success');

    $this->assertDatabaseHas('cart_items', [
        'product_id' => $product->id,
        'quantity' => 1, // Default quantity
    ]);
});

test('add item to cart validates quantity is numeric', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create();

    $response = $this->actingAs($user)->post(route('cart.store'), [
        'product_id' => $product->id,
        'quantity' => 'not-a-number',
    ]);

    $response->assertSessionHasErrors('quantity');
});

test('authenticated users can update cart item quantity', function () {
    $user = User::factory()->create();
    $cart = Cart::factory()->create(['user_id' => $user->id]);
    $product = Product::factory()->create(['stock_quantity' => 10]);
    $cartItem = CartItem::factory()->create([
        'cart_id' => $cart->id,
        'product_id' => $product->id,
        'quantity' => 2,
    ]);

    $response = $this->actingAs($user)->put(route('cart.update', $cartItem), [
        'quantity' => 5,
    ]);

    $response->assertRedirect()
        ->assertSessionHas('success');

    $this->assertDatabaseHas('cart_items', [
        'id' => $cartItem->id,
        'quantity' => 5,
    ]);
});

test('update cart item validates quantity is required', function () {
    $user = User::factory()->create();
    $cart = Cart::factory()->create(['user_id' => $user->id]);
    $product = Product::factory()->create();
    $cartItem = CartItem::factory()->create([
        'cart_id' => $cart->id,
        'product_id' => $product->id,
    ]);

    $response = $this->actingAs($user)->put(route('cart.update', $cartItem), []);

    $response->assertSessionHasErrors('quantity');
});

test('authenticated users can remove item from cart', function () {
    $user = User::factory()->create();
    $cart = Cart::factory()->create(['user_id' => $user->id]);
    $product = Product::factory()->create();
    $cartItem = CartItem::factory()->create([
        'cart_id' => $cart->id,
        'product_id' => $product->id,
    ]);

    $response = $this->actingAs($user)->delete(route('cart.destroy', $cartItem));

    $response->assertRedirect()
        ->assertSessionHas('success');

    $this->assertDatabaseMissing('cart_items', [
        'id' => $cartItem->id,
    ]);
});

test('users cannot update other users cart items', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $cart = Cart::factory()->create(['user_id' => $user2->id]);
    $product = Product::factory()->create(['stock_quantity' => 10]);
    $cartItem = CartItem::factory()->create([
        'cart_id' => $cart->id,
        'product_id' => $product->id,
    ]);

    $response = $this->actingAs($user1)->put(route('cart.update', $cartItem), [
        'quantity' => 5,
    ]);

    $response->assertRedirect()
        ->assertSessionHas('error', 'Cart item not found');
});

test('users cannot delete other users cart items', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $cart = Cart::factory()->create(['user_id' => $user2->id]);
    $product = Product::factory()->create();
    $cartItem = CartItem::factory()->create([
        'cart_id' => $cart->id,
        'product_id' => $product->id,
    ]);

    $response = $this->actingAs($user1)->delete(route('cart.destroy', $cartItem));

    $response->assertRedirect()
        ->assertSessionHas('error', 'Cart item not found');
});

