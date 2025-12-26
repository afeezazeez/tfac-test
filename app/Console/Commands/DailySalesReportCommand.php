<?php

namespace App\Console\Commands;

use App\Mail\DailySalesReportMail;
use App\Models\OrderItem;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class DailySalesReportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sales:report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send daily sales report to admin';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $today = now()->format('Y-m-d');

        $orderItems = OrderItem::whereHas('order', function ($query) use ($today) {
            $query->whereDate('created_at', $today)
                ->where('status', 'completed');
        })
        ->with('product')
        ->get();

        $salesData = $orderItems->groupBy('product_id')->map(function ($items) {
            $firstItem = $items->first();
            return [
                'name' => $firstItem->product->name,
                'price' => (float) $firstItem->price,
                'quantity' => $items->sum('quantity'),
            ];
        })->values()->toArray();

        $adminEmail = config('app.admin_email', 'admin@example.com');

        Mail::to($adminEmail)->send(new DailySalesReportMail($salesData, $today));

        $this->info("Daily sales report sent to {$adminEmail}");

        return Command::SUCCESS;
    }
}
