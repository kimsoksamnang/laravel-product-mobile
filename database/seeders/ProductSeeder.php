<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $electronics = Category::where('slug', 'electronics')->first();
        $fashion = Category::where('slug', 'fashion-apparel')->first();
        $home = Category::where('slug', 'home-living')->first();
        $beauty = Category::where('slug', 'beauty-care')->first();
        $groceries = Category::where('slug', 'groceries-snacks')->first();
        $sports = Category::where('slug', 'sports-outdoors')->first();

        $products = [
            // Electronics
            [
                'category_id' => $electronics?->id,
                'name' => 'Sony WH-1000XM5 Wireless Headphones',
                'sku' => 'ELC-SONY-001',
                'barcode' => '4905524987123',
                'description' => 'Industry-leading noise canceling with two processors and 8 microphones for unprecedented sound.',
                'price' => 399.99,
                'cost_price' => 280.00,
                'stock_quantity' => 18,
                'low_stock_threshold' => 5,
                'image_path' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=500&auto=format&fit=crop&q=80',
                'status' => 'active',
            ],
            [
                'category_id' => $electronics?->id,
                'name' => 'Mechanical Wireless Keyboard Pro',
                'sku' => 'ELC-KEY-002',
                'barcode' => '8412345678901',
                'description' => 'Compact 75% hot-swappable tactile mechanical keyboard with RGB backlighting and Bluetooth 5.2.',
                'price' => 129.50,
                'cost_price' => 75.00,
                'stock_quantity' => 4, // Low stock!
                'low_stock_threshold' => 5,
                'image_path' => 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?w=500&auto=format&fit=crop&q=80',
                'status' => 'active',
            ],
            [
                'category_id' => $electronics?->id,
                'name' => 'MagSafe Fast Wireless Power Bank 10,000mAh',
                'sku' => 'ELC-PWR-003',
                'barcode' => '8412345678902',
                'description' => 'Pocket-friendly magnetic power bank with 15W wireless charging and digital LED display.',
                'price' => 49.00,
                'cost_price' => 24.00,
                'stock_quantity' => 0, // Out of stock!
                'low_stock_threshold' => 8,
                'image_path' => 'https://images.unsplash.com/photo-1609091839311-d5365f9ff1c5?w=500&auto=format&fit=crop&q=80',
                'status' => 'active',
            ],
            [
                'category_id' => $electronics?->id,
                'name' => 'Smart Fitness Watch Series 8',
                'sku' => 'ELC-WTC-004',
                'barcode' => '8412345678903',
                'description' => 'Water-resistant smartwatch with heart rate, blood oxygen, GPS, and 7-day battery life.',
                'price' => 219.00,
                'cost_price' => 135.00,
                'stock_quantity' => 25,
                'low_stock_threshold' => 6,
                'image_path' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=500&auto=format&fit=crop&q=80',
                'status' => 'active',
            ],

            // Fashion
            [
                'category_id' => $fashion?->id,
                'name' => 'Minimalist Heavyweight Cotton Tee',
                'sku' => 'FSH-TEE-001',
                'barcode' => '7891234567890',
                'description' => '280 GSM premium organic combed cotton oversized boxy silhouette.',
                'price' => 38.00,
                'cost_price' => 14.50,
                'stock_quantity' => 42,
                'low_stock_threshold' => 10,
                'image_path' => 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=500&auto=format&fit=crop&q=80',
                'status' => 'active',
            ],
            [
                'category_id' => $fashion?->id,
                'name' => 'Classic Heritage Leather Chelsea Boots',
                'sku' => 'FSH-BOT-002',
                'barcode' => '7891234567891',
                'description' => 'Full-grain Italian calf leather with Goodyear welt construction and cushioned insoles.',
                'price' => 245.00,
                'cost_price' => 120.00,
                'stock_quantity' => 3, // Low stock!
                'low_stock_threshold' => 4,
                'image_path' => 'https://images.unsplash.com/photo-1638247025967-b4e38f787b76?w=500&auto=format&fit=crop&q=80',
                'status' => 'active',
            ],
            [
                'category_id' => $fashion?->id,
                'name' => 'Polarized Tortoiseshell Sunglasses',
                'sku' => 'FSH-SUN-003',
                'barcode' => '7891234567892',
                'description' => 'UV400 scratch-resistant polarized lenses with handcrafted bio-acetate frame.',
                'price' => 85.00,
                'cost_price' => 32.00,
                'stock_quantity' => 15,
                'low_stock_threshold' => 5,
                'image_path' => 'https://images.unsplash.com/photo-1511499767150-a48a237f0083?w=500&auto=format&fit=crop&q=80',
                'status' => 'active',
            ],

            // Home & Living
            [
                'category_id' => $home?->id,
                'name' => 'Ceramic Pour-Over Coffee Dripper Set',
                'sku' => 'HOM-COF-001',
                'barcode' => '6543219876543',
                'description' => 'Artisanal matte ceramic dripper with wooden collar stand and borosilicate glass server.',
                'price' => 54.00,
                'cost_price' => 22.00,
                'stock_quantity' => 12,
                'low_stock_threshold' => 4,
                'image_path' => 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=500&auto=format&fit=crop&q=80',
                'status' => 'active',
            ],
            [
                'category_id' => $home?->id,
                'name' => 'Smart Ambient Sunset Desk Lamp',
                'sku' => 'HOM-LMP-002',
                'barcode' => '6543219876544',
                'description' => 'Adjustable color temperature LED lamp with app control, timer, and wireless phone charging base.',
                'price' => 69.90,
                'cost_price' => 35.00,
                'stock_quantity' => 2, // Low stock!
                'low_stock_threshold' => 5,
                'image_path' => 'https://images.unsplash.com/photo-1507473885765-e6ed057f782c?w=500&auto=format&fit=crop&q=80',
                'status' => 'active',
            ],

            // Beauty & Care
            [
                'category_id' => $beauty?->id,
                'name' => 'Hyaluronic Acid Hydrating Serum (50ml)',
                'sku' => 'BTY-SRM-001',
                'barcode' => '3216549870123',
                'description' => 'Multi-depth molecular weight serum with pure hyaluronic acid and vitamin B5.',
                'price' => 28.50,
                'cost_price' => 9.00,
                'stock_quantity' => 35,
                'low_stock_threshold' => 8,
                'image_path' => 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?w=500&auto=format&fit=crop&q=80',
                'status' => 'active',
            ],
            [
                'category_id' => $beauty?->id,
                'name' => 'Botanical Eau De Parfum - Bergamot & Amber (100ml)',
                'sku' => 'BTY-PRF-002',
                'barcode' => '3216549870124',
                'description' => 'Unisex artisanal fragrance with top notes of Calabrian bergamot and warm cedarwood.',
                'price' => 110.00,
                'cost_price' => 45.00,
                'stock_quantity' => 0, // Out of stock!
                'low_stock_threshold' => 3,
                'image_path' => 'https://images.unsplash.com/photo-1592945403244-b3fbafd7f539?w=500&auto=format&fit=crop&q=80',
                'status' => 'active',
            ],

            // Groceries
            [
                'category_id' => $groceries?->id,
                'name' => 'Single Origin Ethiopian Yirgacheffe Beans (1kg)',
                'sku' => 'GRO-COF-001',
                'barcode' => '9873216540123',
                'description' => 'Light roast specialty beans with floral jasmine aroma and bright citrus peach acidity.',
                'price' => 34.00,
                'cost_price' => 18.00,
                'stock_quantity' => 20,
                'low_stock_threshold' => 5,
                'image_path' => 'https://images.unsplash.com/photo-1559056199-641a0ac8b55e?w=500&auto=format&fit=crop&q=80',
                'status' => 'active',
            ],

            // Sports
            [
                'category_id' => $sports?->id,
                'name' => 'Insulated Stainless Steel Water Flask (32oz)',
                'sku' => 'SPT-FLK-001',
                'barcode' => '4567891234567',
                'description' => 'Double-wall vacuum insulation keeps liquids ice cold for 24 hours or hot for 12 hours.',
                'price' => 32.00,
                'cost_price' => 12.00,
                'stock_quantity' => 50,
                'low_stock_threshold' => 10,
                'image_path' => 'https://images.unsplash.com/photo-1602143407151-7111542de6e8?w=500&auto=format&fit=crop&q=80',
                'status' => 'active',
            ],
            [
                'category_id' => $sports?->id,
                'name' => 'Eco Non-Slip High Density Yoga Mat',
                'sku' => 'SPT-YGA-002',
                'barcode' => '4567891234568',
                'description' => '6mm thick natural tree rubber with alignment laser-etched guidelines and carrying strap.',
                'price' => 58.00,
                'cost_price' => 26.00,
                'stock_quantity' => 1, // Low stock!
                'low_stock_threshold' => 5,
                'image_path' => 'https://images.unsplash.com/photo-1601925260368-ae2f83cf8b7f?w=500&auto=format&fit=crop&q=80',
                'status' => 'active',
            ],
        ];

        foreach ($products as $pData) {
            $product = Product::updateOrCreate(['sku' => $pData['sku']], $pData);

            // Record initial stock entry
            if ($product->stock_quantity > 0) {
                StockMovement::firstOrCreate(
                    [
                        'product_id' => $product->id,
                        'reason' => 'Initial Inventory Setup',
                    ],
                    [
                        'type' => 'in',
                        'quantity' => $product->stock_quantity,
                        'balance_after' => $product->stock_quantity,
                    ]
                );
            }
        }
    }
}
