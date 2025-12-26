<?php

use App\Jobs\LowStockNotificationJob;
use App\Mail\LowStockNotificationMail;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

beforeEach(function () {
    Mail::fake();
});

test('job sends low stock notification email to admin', function () {
    $product = Product::factory()->create([
        'name' => 'Test Product',
        'stock_quantity' => 3,
    ]);
    $threshold = config('app.low_stock_threshold', 5);
    $adminEmail = config('app.admin_email', 'admin@example.com');

    $job = new LowStockNotificationJob($product);
    $job->handle();

    Mail::assertSent(LowStockNotificationMail::class, function ($mail) use ($product, $adminEmail, $threshold) {
        return $mail->hasTo($adminEmail)
            && $mail->product->id === $product->id
            && $mail->threshold === $threshold;
    });
});

