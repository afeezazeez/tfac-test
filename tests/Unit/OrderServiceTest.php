<?php

use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->orderService = app(OrderService::class);
});

test('create order from cart creates order with correct total', function () {
    $product1 = Product::factory()->create(['price' => 10.00, 'stock_quantity' => 10]);
    $product2 = Product::factory()->create(['price' => 20.00, 'stock_quantity' => 10]);
    
    $cartItem1 = new CartItem(['product_id' => $product1->id, 'quantity' => 2]);
    $cartItem1->setRelation('product', $product1);
    
    $cartItem2 = new CartItem(['product_id' => $product2->id, 'quantity' => 1]);
    $cartItem2->setRelation('product', $product2);

    $order = $this->orderService->createOrderFromCart($this->user->id, [$cartItem1, $cartItem2]);

    expect($order)->toBeInstanceOf(Order::class)
        ->and($order->user_id)->toBe($this->user->id)
        ->and((float) $order->total)->toBe(40.00)
        ->and($order->status)->toBe('completed')
        ->and($order->order_id)->toStartWith('ORD-');
});

test('create order from cart creates order items correctly', function () {
    $product1 = Product::factory()->create(['price' => 10.00, 'stock_quantity' => 10]);
    $product2 = Product::factory()->create(['price' => 20.00, 'stock_quantity' => 10]);
    
    $cartItem1 = new CartItem(['product_id' => $product1->id, 'quantity' => 2]);
    $cartItem1->setRelation('product', $product1);
    
    $cartItem2 = new CartItem(['product_id' => $product2->id, 'quantity' => 1]);
    $cartItem2->setRelation('product', $product2);

    $order = $this->orderService->createOrderFromCart($this->user->id, [$cartItem1, $cartItem2]);

    expect(OrderItem::where('order_id', $order->id)->count())->toBe(2)
        ->and(OrderItem::where('product_id', $product1->id)->first()->quantity)->toBe(2)
        ->and((float) OrderItem::where('product_id', $product1->id)->first()->price)->toBe(10.00)
        ->and(OrderItem::where('product_id', $product2->id)->first()->quantity)->toBe(1)
        ->and((float) OrderItem::where('product_id', $product2->id)->first()->price)->toBe(20.00);
});

test('create order from cart decrements product stock', function () {
    $product = Product::factory()->create(['stock_quantity' => 10]);
    
    $cartItem = new CartItem(['product_id' => $product->id, 'quantity' => 3]);
    $cartItem->setRelation('product', $product);

    $this->orderService->createOrderFromCart($this->user->id, [$cartItem]);

    $product->refresh();
    expect($product->stock_quantity)->toBe(7);
});

test('create order from cart generates unique order id', function () {
    $product = Product::factory()->create(['stock_quantity' => 10]);
    
    $cartItem = new CartItem(['product_id' => $product->id, 'quantity' => 1]);
    $cartItem->setRelation('product', $product);

    $order1 = $this->orderService->createOrderFromCart($this->user->id, [$cartItem]);
    $order2 = $this->orderService->createOrderFromCart($this->user->id, [$cartItem]);

    expect($order1->order_id)->not->toBe($order2->order_id);
});

