<?php

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('product is in stock when stock quantity is greater than zero', function () {
    $product = Product::factory()->create(['stock_quantity' => 10]);

    expect($product->isInStock())->toBeTrue();
});

test('product is not in stock when stock quantity is zero', function () {
    $product = Product::factory()->create(['stock_quantity' => 0]);

    expect($product->isInStock())->toBeFalse();
});

test('product is out of stock when stock quantity is zero', function () {
    $product = Product::factory()->create(['stock_quantity' => 0]);

    expect($product->isOutOfStock())->toBeTrue();
});

test('product is not out of stock when stock quantity is greater than zero', function () {
    $product = Product::factory()->create(['stock_quantity' => 5]);

    expect($product->isOutOfStock())->toBeFalse();
});

test('product is low stock when quantity is at or below threshold', function () {
    $threshold = 5;
    $product = Product::factory()->create(['stock_quantity' => $threshold]);

    expect($product->isLowStock($threshold))->toBeTrue();
});

test('product is low stock when quantity is below threshold', function () {
    $threshold = 5;
    $product = Product::factory()->create(['stock_quantity' => 3]);

    expect($product->isLowStock($threshold))->toBeTrue();
});

test('product is not low stock when quantity is above threshold', function () {
    $threshold = 5;
    $product = Product::factory()->create(['stock_quantity' => 10]);

    expect($product->isLowStock($threshold))->toBeFalse();
});

test('product is not low stock when out of stock', function () {
    $threshold = 5;
    $product = Product::factory()->create(['stock_quantity' => 0]);

    expect($product->isLowStock($threshold))->toBeFalse();
});

test('product has enough stock when quantity is available', function () {
    $product = Product::factory()->create(['stock_quantity' => 10]);

    expect($product->hasEnoughStock(5))->toBeTrue();
    expect($product->hasEnoughStock(10))->toBeTrue();
});

test('product does not have enough stock when quantity exceeds available', function () {
    $product = Product::factory()->create(['stock_quantity' => 5]);

    expect($product->hasEnoughStock(10))->toBeFalse();
});

test('product does not have enough stock when out of stock', function () {
    $product = Product::factory()->create(['stock_quantity' => 0]);

    expect($product->hasEnoughStock(1))->toBeFalse();
});

