<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Electronics',
                'slug' => 'electronics',
                'icon' => 'laptop',
                'color_code' => 'indigo',
                'description' => 'Gadgets, smartphones, accessories, and audio gear',
            ],
            [
                'name' => 'Fashion & Apparel',
                'slug' => 'fashion-apparel',
                'icon' => 'shirt',
                'color_code' => 'pink',
                'description' => 'Clothing, footwear, watches, and streetwear',
            ],
            [
                'name' => 'Home & Living',
                'slug' => 'home-living',
                'icon' => 'home',
                'color_code' => 'amber',
                'description' => 'Furniture, decor, lighting, and kitchenware',
            ],
            [
                'name' => 'Beauty & Care',
                'slug' => 'beauty-care',
                'icon' => 'sparkles',
                'color_code' => 'rose',
                'description' => 'Skincare, wellness, fragrance, and grooming',
            ],
            [
                'name' => 'Groceries & Snacks',
                'slug' => 'groceries-snacks',
                'icon' => 'coffee',
                'color_code' => 'emerald',
                'description' => 'Beverages, artisan coffee, snacks, and organic food',
            ],
            [
                'name' => 'Sports & Outdoors',
                'slug' => 'sports-outdoors',
                'icon' => 'dumbbell',
                'color_code' => 'cyan',
                'description' => 'Fitness gear, outdoor equipment, and activewear',
            ],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(['slug' => $cat['slug']], $cat);
        }
    }
}
