<?php

namespace App\Jobs;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class LowStockNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Product $product)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $adminEmail = config('app.admin_email', 'admin@example.com');
        $threshold = config('app.low_stock_threshold', 5);

        Mail::raw(
            "Product '{$this->product->name}' is running low on stock.\n\n" .
            "Current stock: {$this->product->stock_quantity}\n" .
            "Low stock threshold: {$threshold}\n\n" .
            "Please consider restocking this product.",
            function ($message) use ($adminEmail) {
                $message->to($adminEmail)
                    ->subject('Low Stock Alert: ' . $this->product->name);
            }
        );
    }
}
