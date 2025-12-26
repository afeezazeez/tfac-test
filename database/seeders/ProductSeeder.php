<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            ['name' => 'Wireless Mouse', 'price' => 29.99, 'stock_quantity' => 50],
            ['name' => 'Mechanical Keyboard', 'price' => 89.99, 'stock_quantity' => 3],
            ['name' => 'USB-C Cable', 'price' => 19.99, 'stock_quantity' => 0],
            ['name' => 'Laptop Stand', 'price' => 39.99, 'stock_quantity' => 0],
            ['name' => 'Webcam HD', 'price' => 79.99, 'stock_quantity' => 2],
            ['name' => 'Noise Cancelling Headphones', 'price' => 199.99, 'stock_quantity' => 4],
            ['name' => 'External Hard Drive 1TB', 'price' => 59.99, 'stock_quantity' => 35],
            ['name' => 'USB Flash Drive 64GB', 'price' => 14.99, 'stock_quantity' => 80],
            ['name' => 'Monitor Stand', 'price' => 49.99, 'stock_quantity' => 1],
            ['name' => 'Gaming Chair', 'price' => 299.99, 'stock_quantity' => 0],
            ['name' => 'Desk Organizer', 'price' => 24.99, 'stock_quantity' => 45],
            ['name' => 'Cable Management Kit', 'price' => 12.99, 'stock_quantity' => 60],
            ['name' => 'Wireless Charger', 'price' => 34.99, 'stock_quantity' => 55],
            ['name' => 'Laptop Sleeve', 'price' => 29.99, 'stock_quantity' => 5],
            ['name' => 'Bluetooth Speaker', 'price' => 49.99, 'stock_quantity' => 30],
            ['name' => 'Tablet Stand', 'price' => 19.99, 'stock_quantity' => 50],
            ['name' => 'Power Bank 10000mAh', 'price' => 39.99, 'stock_quantity' => 65],
            ['name' => 'HDMI Cable', 'price' => 15.99, 'stock_quantity' => 70],
            ['name' => 'Screen Protector', 'price' => 9.99, 'stock_quantity' => 90],
            ['name' => 'Laptop Cooling Pad', 'price' => 34.99, 'stock_quantity' => 25],
            ['name' => 'Gaming Mouse Pad', 'price' => 18.99, 'stock_quantity' => 75],
            ['name' => 'USB Hub 4 Port', 'price' => 24.99, 'stock_quantity' => 50],
            ['name' => 'Microphone USB', 'price' => 69.99, 'stock_quantity' => 20],
            ['name' => 'Ring Light LED', 'price' => 44.99, 'stock_quantity' => 35],
            ['name' => 'Monitor Arm Mount', 'price' => 79.99, 'stock_quantity' => 15],
            ['name' => 'Laptop Bag', 'price' => 54.99, 'stock_quantity' => 30],
            ['name' => 'Ethernet Cable 10ft', 'price' => 11.99, 'stock_quantity' => 85],
            ['name' => 'Keyboard Wrist Rest', 'price' => 16.99, 'stock_quantity' => 60],
            ['name' => 'USB Extension Cable', 'price' => 8.99, 'stock_quantity' => 100],
            ['name' => 'Portable Monitor 15.6"', 'price' => 199.99, 'stock_quantity' => 10],
            ['name' => 'Laptop Lock', 'price' => 22.99, 'stock_quantity' => 40],
            ['name' => 'Wireless Presenter Remote', 'price' => 29.99, 'stock_quantity' => 3],
            ['name' => 'USB Webcam Cover', 'price' => 4.99, 'stock_quantity' => 95],
            ['name' => 'Desk Mat Large', 'price' => 32.99, 'stock_quantity' => 45],
            ['name' => 'Phone Stand Adjustable', 'price' => 13.99, 'stock_quantity' => 70],
            ['name' => 'Bluetooth Earbuds', 'price' => 59.99, 'stock_quantity' => 55],
            ['name' => 'USB-C to USB Adapter', 'price' => 7.99, 'stock_quantity' => 90],
            ['name' => 'Laptop Privacy Screen', 'price' => 39.99, 'stock_quantity' => 25],
            ['name' => 'Docking Station USB-C', 'price' => 129.99, 'stock_quantity' => 15],
            ['name' => 'Wireless Keyboard Mouse Combo', 'price' => 44.99, 'stock_quantity' => 40],
            ['name' => 'Monitor Privacy Filter', 'price' => 49.99, 'stock_quantity' => 20],
            ['name' => 'Laptop Battery Replacement', 'price' => 89.99, 'stock_quantity' => 0],
            ['name' => 'Mechanical Keyboard RGB', 'price' => 129.99, 'stock_quantity' => 0],
            ['name' => 'USB-C Hub with HDMI', 'price' => 54.99, 'stock_quantity' => 35],
            ['name' => 'Gaming Headset', 'price' => 79.99, 'stock_quantity' => 30],
            ['name' => 'Desk Lamp LED', 'price' => 34.99, 'stock_quantity' => 50],
            ['name' => 'USB Mini Fan', 'price' => 12.99, 'stock_quantity' => 80],
            ['name' => 'Laptop Skin Sticker', 'price' => 16.99, 'stock_quantity' => 65],
            ['name' => 'Monitor Cleaning Kit', 'price' => 9.99, 'stock_quantity' => 75],
            ['name' => 'Wireless Number Pad', 'price' => 24.99, 'stock_quantity' => 4],
            ['name' => 'USB-C to Ethernet Adapter', 'price' => 19.99, 'stock_quantity' => 55],
            ['name' => 'Laptop Charger 65W', 'price' => 39.99, 'stock_quantity' => 30],
            ['name' => 'Bluetooth Mouse', 'price' => 27.99, 'stock_quantity' => 60],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
