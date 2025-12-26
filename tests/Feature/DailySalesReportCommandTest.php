<?php

use App\Console\Commands\DailySalesReportCommand;
use App\Mail\DailySalesReportMail;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

beforeEach(function () {
    Mail::fake();
});

test('command sends daily sales report email with correct data', function () {
    $user = User::factory()->create();
    $product1 = Product::factory()->create(['name' => 'Product 1', 'price' => 10.00]);
    $product2 = Product::factory()->create(['name' => 'Product 2', 'price' => 20.00]);

    $order = Order::factory()->create([
        'user_id' => $user->id,
        'status' => 'completed',
        'created_at' => now(),
    ]);

    OrderItem::factory()->create([
        'order_id' => $order->id,
        'product_id' => $product1->id,
        'quantity' => 2,
        'price' => 10.00,
    ]);

    OrderItem::factory()->create([
        'order_id' => $order->id,
        'product_id' => $product2->id,
        'quantity' => 1,
        'price' => 20.00,
    ]);

    $this->artisan(DailySalesReportCommand::class)
        ->assertSuccessful();

    Mail::assertSent(DailySalesReportMail::class, function ($mail) {
        $salesData = $mail->salesData;
        
        return count($salesData) === 2
            && $mail->hasTo(config('app.admin_email', 'admin@example.com'))
            && collect($salesData)->contains(fn ($item) => $item['name'] === 'Product 1' && $item['quantity'] === 2)
            && collect($salesData)->contains(fn ($item) => $item['name'] === 'Product 2' && $item['quantity'] === 1);
    });
});

test('command only includes orders from today', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['name' => 'Product 1', 'price' => 10.00]);

    $todayOrder = Order::factory()->create([
        'user_id' => $user->id,
        'status' => 'completed',
        'created_at' => now(),
    ]);

    $yesterdayOrder = Order::factory()->create([
        'user_id' => $user->id,
        'status' => 'completed',
        'created_at' => now()->subDay(),
    ]);

    OrderItem::factory()->create([
        'order_id' => $todayOrder->id,
        'product_id' => $product->id,
        'quantity' => 1,
        'price' => 10.00,
    ]);

    OrderItem::factory()->create([
        'order_id' => $yesterdayOrder->id,
        'product_id' => $product->id,
        'quantity' => 5,
        'price' => 10.00,
    ]);

    $this->artisan(DailySalesReportCommand::class)
        ->assertSuccessful();

    Mail::assertSent(DailySalesReportMail::class, function ($mail) {
        $salesData = $mail->salesData;
        
        return count($salesData) === 1
            && $salesData[0]['quantity'] === 1;
    });
});

test('command only includes completed orders', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['name' => 'Product 1', 'price' => 10.00]);

    $completedOrder = Order::factory()->create([
        'user_id' => $user->id,
        'status' => 'completed',
        'created_at' => now(),
    ]);

    $pendingOrder = Order::factory()->create([
        'user_id' => $user->id,
        'status' => 'pending',
        'created_at' => now(),
    ]);

    OrderItem::factory()->create([
        'order_id' => $completedOrder->id,
        'product_id' => $product->id,
        'quantity' => 1,
        'price' => 10.00,
    ]);

    OrderItem::factory()->create([
        'order_id' => $pendingOrder->id,
        'product_id' => $product->id,
        'quantity' => 5,
        'price' => 10.00,
    ]);

    $this->artisan(DailySalesReportCommand::class)
        ->assertSuccessful();

    Mail::assertSent(DailySalesReportMail::class, function ($mail) {
        $salesData = $mail->salesData;
        
        return count($salesData) === 1
            && $salesData[0]['quantity'] === 1;
    });
});

test('command groups order items by product', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['name' => 'Product 1', 'price' => 10.00]);

    $order1 = Order::factory()->create([
        'user_id' => $user->id,
        'status' => 'completed',
        'created_at' => now(),
    ]);

    $order2 = Order::factory()->create([
        'user_id' => $user->id,
        'status' => 'completed',
        'created_at' => now(),
    ]);

    OrderItem::factory()->create([
        'order_id' => $order1->id,
        'product_id' => $product->id,
        'quantity' => 2,
        'price' => 10.00,
    ]);

    OrderItem::factory()->create([
        'order_id' => $order2->id,
        'product_id' => $product->id,
        'quantity' => 3,
        'price' => 10.00,
    ]);

    $this->artisan(DailySalesReportCommand::class)
        ->assertSuccessful();

    Mail::assertSent(DailySalesReportMail::class, function ($mail) {
        $salesData = $mail->salesData;
        
        return count($salesData) === 1
            && $salesData[0]['quantity'] === 5; // 2 + 3
    });
});

test('command handles no sales scenario', function () {
    $this->artisan(DailySalesReportCommand::class)
        ->assertSuccessful();

    Mail::assertSent(DailySalesReportMail::class, function ($mail) {
        return count($mail->salesData) === 0;
    });
});

