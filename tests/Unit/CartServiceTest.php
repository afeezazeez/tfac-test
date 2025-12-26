<?php

use App\Exceptions\ClientErrorException;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->cartService = app(CartService::class);
});

test('get or create cart creates cart when it does not exist', function () {
    $cart = $this->cartService->getOrCreateCart($this->user->id);

    expect($cart)->toBeInstanceOf(Cart::class)
        ->and($cart->user_id)->toBe($this->user->id)
        ->and(Cart::where('user_id', $this->user->id)->count())->toBe(1);
});

test('get or create cart returns existing cart', function () {
    $existingCart = Cart::factory()->create(['user_id' => $this->user->id]);

    $cart = $this->cartService->getOrCreateCart($this->user->id);

    expect($cart->id)->toBe($existingCart->id);
});

test('get cart with items returns null when cart does not exist', function () {
    $cart = $this->cartService->getCartWithItems($this->user->id);

    expect($cart)->toBeNull();
});

test('get cart with items returns cart with items and products', function () {
    $cart = Cart::factory()->create(['user_id' => $this->user->id]);
    $product = Product::factory()->create();
    CartItem::factory()->create([
        'cart_id' => $cart->id,
        'product_id' => $product->id,
    ]);

    $cart = $this->cartService->getCartWithItems($this->user->id);

    expect($cart)->not->toBeNull()
        ->and($cart->items)->toHaveCount(1)
        ->and($cart->items->first()->product)->not->toBeNull();
});

test('add item to cart creates new cart item', function () {
    $product = Product::factory()->create(['stock_quantity' => 10]);

    $cartItem = $this->cartService->addItem($this->user->id, $product->id, 2);

    expect($cartItem)->toBeInstanceOf(CartItem::class)
        ->and($cartItem->quantity)->toBe(2)
        ->and($cartItem->product_id)->toBe($product->id)
        ->and(CartItem::count())->toBe(1);
});

test('add item to cart updates quantity when item already exists', function () {
    $cart = Cart::factory()->create(['user_id' => $this->user->id]);
    $product = Product::factory()->create(['stock_quantity' => 10]);
    $existingItem = CartItem::factory()->create([
        'cart_id' => $cart->id,
        'product_id' => $product->id,
        'quantity' => 3,
    ]);

    $cartItem = $this->cartService->addItem($this->user->id, $product->id, 2);

    expect($cartItem->id)->toBe($existingItem->id)
        ->and($cartItem->quantity)->toBe(5)
        ->and(CartItem::count())->toBe(1);
});

test('add item to cart throws exception when product not found', function () {
    expect(fn () => $this->cartService->addItem($this->user->id, 999, 1))
        ->toThrow(ClientErrorException::class, 'Product not found');
});

test('add item to cart throws exception when product is out of stock', function () {
    $product = Product::factory()->create(['stock_quantity' => 0]);

    expect(fn () => $this->cartService->addItem($this->user->id, $product->id, 1))
        ->toThrow(ClientErrorException::class, 'Product is out of stock');
});

test('add item to cart throws exception when insufficient stock', function () {
    $product = Product::factory()->create(['stock_quantity' => 5]);

    expect(fn () => $this->cartService->addItem($this->user->id, $product->id, 10))
        ->toThrow(ClientErrorException::class, 'Insufficient stock available');
});

test('update item quantity updates cart item', function () {
    $cart = Cart::factory()->create(['user_id' => $this->user->id]);
    $product = Product::factory()->create(['stock_quantity' => 10]);
    $cartItem = CartItem::factory()->create([
        'cart_id' => $cart->id,
        'product_id' => $product->id,
        'quantity' => 2,
    ]);

    $updatedItem = $this->cartService->updateItemQuantity($this->user->id, $cartItem->id, 5);

    expect($updatedItem->quantity)->toBe(5);
});

test('update item quantity deletes item when quantity is zero', function () {
    $cart = Cart::factory()->create(['user_id' => $this->user->id]);
    $product = Product::factory()->create(['stock_quantity' => 10]);
    $cartItem = CartItem::factory()->create([
        'cart_id' => $cart->id,
        'product_id' => $product->id,
        'quantity' => 2,
    ]);

    $result = $this->cartService->updateItemQuantity($this->user->id, $cartItem->id, 0);

    expect($result)->toBeNull()
        ->and(CartItem::find($cartItem->id))->toBeNull();
});

test('update item quantity throws exception when cart item not found', function () {
    expect(fn () => $this->cartService->updateItemQuantity($this->user->id, 999, 5))
        ->toThrow(ClientErrorException::class, 'Cart item not found');
});

test('update item quantity throws exception when insufficient stock', function () {
    $cart = Cart::factory()->create(['user_id' => $this->user->id]);
    $product = Product::factory()->create(['stock_quantity' => 5]);
    $cartItem = CartItem::factory()->create([
        'cart_id' => $cart->id,
        'product_id' => $product->id,
        'quantity' => 2,
    ]);

    expect(fn () => $this->cartService->updateItemQuantity($this->user->id, $cartItem->id, 10))
        ->toThrow(ClientErrorException::class, 'Insufficient stock available');
});

test('remove item deletes cart item', function () {
    $cart = Cart::factory()->create(['user_id' => $this->user->id]);
    $product = Product::factory()->create();
    $cartItem = CartItem::factory()->create([
        'cart_id' => $cart->id,
        'product_id' => $product->id,
    ]);

    $result = $this->cartService->removeItem($this->user->id, $cartItem->id);

    expect($result)->toBeTrue()
        ->and(CartItem::find($cartItem->id))->toBeNull();
});

test('remove item throws exception when cart item not found', function () {
    expect(fn () => $this->cartService->removeItem($this->user->id, 999))
        ->toThrow(ClientErrorException::class, 'Cart item not found');
});

test('clear cart deletes all cart items', function () {
    $cart = Cart::factory()->create(['user_id' => $this->user->id]);
    $product1 = Product::factory()->create();
    $product2 = Product::factory()->create();
    CartItem::factory()->create(['cart_id' => $cart->id, 'product_id' => $product1->id]);
    CartItem::factory()->create(['cart_id' => $cart->id, 'product_id' => $product2->id]);

    $this->cartService->clearCart($this->user->id);

    expect(CartItem::where('cart_id', $cart->id)->count())->toBe(0);
});

test('clear cart does nothing when cart does not exist', function () {
    $this->cartService->clearCart($this->user->id);

    expect(CartItem::count())->toBe(0);
});

test('get item count returns zero when cart does not exist', function () {
    $count = $this->cartService->getItemCount($this->user->id);

    expect($count)->toBe(0);
});

test('get item count returns sum of all item quantities', function () {
    $cart = Cart::factory()->create(['user_id' => $this->user->id]);
    $product1 = Product::factory()->create();
    $product2 = Product::factory()->create();
    CartItem::factory()->create(['cart_id' => $cart->id, 'product_id' => $product1->id, 'quantity' => 3]);
    CartItem::factory()->create(['cart_id' => $cart->id, 'product_id' => $product2->id, 'quantity' => 2]);

    $count = $this->cartService->getItemCount($this->user->id);

    expect($count)->toBe(5);
});

