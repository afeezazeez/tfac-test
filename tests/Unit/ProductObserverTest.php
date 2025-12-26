<?php

use App\Jobs\LowStockNotificationJob;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

beforeEach(function () {
    Queue::fake();
});

test('observer dispatches job when stock crosses low stock threshold', function () {
    $threshold = config('app.low_stock_threshold', 5);
    $product = Product::factory()->create(['stock_quantity' => $threshold + 1]);

    $product->update(['stock_quantity' => $threshold]);

    Queue::assertPushed(LowStockNotificationJob::class, function ($job) use ($product) {
        return $job->product->id === $product->id;
    });
});

test('observer does not dispatch job when stock is already below threshold', function () {
    $threshold = config('app.low_stock_threshold', 5);
    $product = Product::factory()->create(['stock_quantity' => $threshold - 1]);

    $product->update(['stock_quantity' => $threshold - 2]);

    Queue::assertNothingPushed();
});

test('observer does not dispatch job when stock goes from below to above threshold', function () {
    $threshold = config('app.low_stock_threshold', 5);
    $product = Product::factory()->create(['stock_quantity' => $threshold - 1]);

    $product->update(['stock_quantity' => $threshold + 1]);

    Queue::assertNothingPushed();
});

test('observer does not dispatch job when stock quantity is not changed', function () {
    $product = Product::factory()->create(['stock_quantity' => 3, 'name' => 'Test Product']);

    $product->update(['name' => 'Updated Name']);

    Queue::assertNothingPushed();
});

test('observer dispatches job when stock goes from above threshold to zero', function () {
    $threshold = config('app.low_stock_threshold', 5);
    $product = Product::factory()->create(['stock_quantity' => $threshold + 1]);

    $product->update(['stock_quantity' => 0]);

    Queue::assertPushed(LowStockNotificationJob::class);
});

