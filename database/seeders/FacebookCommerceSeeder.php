<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\FacebookPage;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderMessage;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class FacebookCommerceSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Default Users
        $user = User::updateOrCreate(
            ['email' => 'samnang@example.com'],
            [
                'name' => 'Samnang Heng',
                'password' => Hash::make('password'),
                'facebook_user_id' => 'fb_1029384756',
                'facebook_connected_at' => Carbon::now()->subMonths(2),
                'is_system_admin' => true,
                'avatar_url' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=150&auto=format&fit=crop&q=80',
                'phone' => '+855 12 889 900',
                'role' => 'Shop Owner & Administrator',
                'fb_profile_url' => 'https://facebook.com/samnang.heng',
                'bio' => 'E-commerce entrepreneur managing multiple Facebook retail pages across Phnom Penh & Siem Reap.',
            ]
        );

        $user2 = User::updateOrCreate(
            ['email' => 'dara.sok@example.com'],
            [
                'name' => 'Dara Sok',
                'password' => Hash::make('password'),
                'facebook_user_id' => 'fb_5544332211',
                'facebook_connected_at' => Carbon::now()->subDays(15),
                'is_system_admin' => false,
                'avatar_url' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150&auto=format&fit=crop&q=80',
                'phone' => '+855 77 123 456',
                'role' => 'Merchant & Tech Manager',
                'fb_profile_url' => 'https://facebook.com/dara.tech',
                'bio' => 'Independent electronics reseller and boutique associate.',
            ]
        );

        $user3 = User::updateOrCreate(
            ['email' => 'sreymom@example.com'],
            [
                'name' => 'Sreymom Meas',
                'password' => Hash::make('password'),
                'facebook_user_id' => null,
                'facebook_connected_at' => null,
                'is_system_admin' => false,
                'avatar_url' => 'https://images.unsplash.com/photo-1517841905240-472988babdf9?w=150&auto=format&fit=crop&q=80',
                'phone' => '+855 88 555 777',
                'role' => 'Customer Care Staff',
                'fb_profile_url' => null,
                'bio' => 'Social media customer support specialist awaiting Facebook connection.',
            ]
        );

        // 2. Create Facebook Pages (Shops)
        $fashionPage = FacebookPage::updateOrCreate(
            ['page_id' => '108249827401928'],
            [
                'name' => 'Bella Fashion Boutique',
                'category' => 'Clothing & Fashion Apparel',
                'avatar_url' => 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=200&auto=format&fit=crop&q=80',
                'cover_url' => 'https://images.unsplash.com/photo-1489987707025-afc232f7ea0f?w=800&auto=format&fit=crop&q=80',
                'followers_count' => 48200,
                'phone' => '+855 23 999 111',
                'email' => 'contact@bellaboutique.shop',
                'about' => 'Trendy fashion apparel, minimalist streetwear, and classic leather accessories with same-day delivery in Phnom Penh.',
                'is_active' => true,
                'ai_order_agent_enabled' => true,
            ]
        );

        $techPage = FacebookPage::updateOrCreate(
            ['page_id' => '109840294812390'],
            [
                'name' => 'TechNest Electronics Cambodia',
                'category' => 'Consumer Electronics & Gadgets',
                'avatar_url' => 'https://images.unsplash.com/photo-1550009158-9ebf69173e03?w=200&auto=format&fit=crop&q=80',
                'cover_url' => 'https://images.unsplash.com/photo-1526738549149-8e07eca6c147?w=800&auto=format&fit=crop&q=80',
                'followers_count' => 129500,
                'phone' => '+855 12 777 222',
                'email' => 'sales@technest.biz',
                'about' => 'Authorized retailer of premium audio, keyboards, gaming accessories, and smart devices with nationwide express shipping.',
                'is_active' => true,
                'ai_order_agent_enabled' => false,
            ]
        );

        // Attach User to both Facebook Pages with roles (Many-to-Many)
        $user->facebookPages()->syncWithoutDetaching([
            $fashionPage->id => ['role' => 'owner'],
            $techPage->id => ['role' => 'admin'],
        ]);

        $user2->facebookPages()->syncWithoutDetaching([
            $techPage->id => ['role' => 'moderator'],
        ]);

        // 3. Associate Categories with Pages
        Category::whereIn('slug', ['fashion-apparel', 'beauty-care'])
            ->update(['facebook_page_id' => $fashionPage->id]);

        Category::whereIn('slug', ['electronics', 'home-living', 'sports-outdoors', 'groceries-snacks'])
            ->update(['facebook_page_id' => $techPage->id]);

        // 4. Associate Products with Pages
        Product::whereHas('category', function ($q) {
            $q->whereIn('slug', ['fashion-apparel', 'beauty-care']);
        })->update(['facebook_page_id' => $fashionPage->id]);

        Product::whereHas('category', function ($q) {
            $q->whereIn('slug', ['electronics', 'home-living', 'sports-outdoors', 'groceries-snacks']);
        })->update(['facebook_page_id' => $techPage->id]);

        // 5. Seed Open Orders
        // Order 1: Pending (Bella Boutique)
        $teeProduct = Product::where('sku', 'FSH-TEE-001')->first() ?? Product::first();
        if ($teeProduct) {
            $order1 = Order::updateOrCreate(
                ['order_number' => 'FB-ORD-7821'],
                [
                    'facebook_page_id' => $fashionPage->id,
                    'customer_name' => 'Sokha Chan',
                    'customer_phone' => '012 345 678',
                    'customer_address' => '#24B, St 310, BKK1, Phnom Penh',
                    'customer_avatar' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=150&auto=format&fit=crop&q=80',
                    'customer_fb_id' => 'fb_cust_99812',
                    'source' => 'messenger',
                    'status' => 'pending',
                    'payment_status' => 'cod',
                    'subtotal' => $teeProduct->price * 2,
                    'shipping_fee' => 2.00,
                    'discount_amount' => 0.00,
                    'total_amount' => ($teeProduct->price * 2) + 2.00,
                    'notes' => 'Customer requested delivery before 5 PM.',
                ]
            );

            OrderItem::firstOrCreate(
                ['order_id' => $order1->id, 'product_id' => $teeProduct->id],
                [
                    'product_name' => $teeProduct->name,
                    'product_sku' => $teeProduct->sku,
                    'unit_price' => $teeProduct->price,
                    'quantity' => 2,
                    'subtotal' => $teeProduct->price * 2,
                ]
            );

            if ($order1->messages()->count() === 0) {
                OrderMessage::create([
                    'order_id' => $order1->id,
                    'sender_type' => 'customer',
                    'sender_name' => 'Sokha Chan',
                    'type' => 'messenger',
                    'message' => 'Hello admin! Is the Heavyweight Cotton Tee still available in XL black?',
                    'created_at' => Carbon::now()->subHours(3),
                ]);
                OrderMessage::create([
                    'order_id' => $order1->id,
                    'sender_type' => 'page_agent',
                    'sender_name' => 'Bella Fashion Team',
                    'type' => 'messenger',
                    'message' => 'Hi Sokha! Yes, black XL is in stock. Price is $' . number_format($teeProduct->price, 2) . '. If you order 2 we can offer express dispatch today!',
                    'created_at' => Carbon::now()->subHours(2)->subMinutes(40),
                ]);
                OrderMessage::create([
                    'order_id' => $order1->id,
                    'sender_type' => 'customer',
                    'sender_name' => 'Sokha Chan',
                    'type' => 'messenger',
                    'message' => 'Great! I will take 2 pcs black XL. Please send via COD to #24B, St 310, BKK1, Phnom Penh. My phone is 012 345 678.',
                    'created_at' => Carbon::now()->subHours(2)->subMinutes(15),
                ]);
                OrderMessage::create([
                    'order_id' => $order1->id,
                    'sender_type' => 'system',
                    'sender_name' => 'StockPilot Order System',
                    'type' => 'system_event',
                    'message' => 'Order created from Messenger conversation. Status set to Pending Review.',
                    'created_at' => Carbon::now()->subHours(2),
                ]);
            }
        }

        // Order 2: Confirmed (Bella Boutique)
        $bootsProduct = Product::where('sku', 'FSH-BOT-002')->first();
        if ($bootsProduct) {
            $order2 = Order::updateOrCreate(
                ['order_number' => 'FB-ORD-7822'],
                [
                    'facebook_page_id' => $fashionPage->id,
                    'customer_name' => 'Davy Pich',
                    'customer_phone' => '098 765 432',
                    'customer_address' => '#15, St 271, Tuol Tompoung, Phnom Penh',
                    'customer_avatar' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=150&auto=format&fit=crop&q=80',
                    'customer_fb_id' => 'fb_cust_55210',
                    'source' => 'comment',
                    'status' => 'confirmed',
                    'payment_status' => 'cod',
                    'subtotal' => $bootsProduct->price,
                    'shipping_fee' => 0.00,
                    'discount_amount' => 10.00,
                    'total_amount' => max(0, $bootsProduct->price - 10.00),
                    'notes' => 'Applied $10 voucher promo code.',
                ]
            );

            OrderItem::firstOrCreate(
                ['order_id' => $order2->id, 'product_id' => $bootsProduct->id],
                [
                    'product_name' => $bootsProduct->name,
                    'product_sku' => $bootsProduct->sku,
                    'unit_price' => $bootsProduct->price,
                    'quantity' => 1,
                    'subtotal' => $bootsProduct->price,
                ]
            );

            if ($order2->messages()->count() === 0) {
                OrderMessage::create([
                    'order_id' => $order2->id,
                    'sender_type' => 'customer',
                    'sender_name' => 'Davy Pich',
                    'type' => 'comment',
                    'message' => 'CF Chelsea Boots size 41 brown please! Sent you a private inbox message.',
                    'created_at' => Carbon::now()->subHours(5),
                ]);
                OrderMessage::create([
                    'order_id' => $order2->id,
                    'sender_type' => 'page_agent',
                    'sender_name' => 'Bella Fashion Team',
                    'type' => 'messenger',
                    'message' => 'Hello Davy! Confirmed in inbox. Size 41 Chelsea boots reserved. Applied special $10 promo discount for you.',
                    'created_at' => Carbon::now()->subHours(4)->subMinutes(30),
                ]);
                OrderMessage::create([
                    'order_id' => $order2->id,
                    'sender_type' => 'customer',
                    'sender_name' => 'Davy Pich',
                    'type' => 'messenger',
                    'message' => 'Confirmed! Address is #15, St 271, Tuol Tompoung. Please call 15 minutes ahead of delivery.',
                    'created_at' => Carbon::now()->subHours(4),
                ]);
                OrderMessage::create([
                    'order_id' => $order2->id,
                    'sender_type' => 'system',
                    'sender_name' => 'StockPilot Order System',
                    'type' => 'system_event',
                    'message' => 'Order verified and moved to Confirmed state.',
                    'created_at' => Carbon::now()->subHours(3)->subMinutes(50),
                ]);
            }
        }

        // Order 3: Shipped (TechNest Electronics)
        $sonyProduct = Product::where('sku', 'ELC-SONY-001')->first();
        if ($sonyProduct) {
            $order3 = Order::updateOrCreate(
                ['order_number' => 'FB-ORD-5501'],
                [
                    'facebook_page_id' => $techPage->id,
                    'customer_name' => 'Rathana Meng',
                    'customer_phone' => '077 112 233',
                    'customer_address' => 'Siem Reap City, near Pub Street & Night Market',
                    'customer_avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150&auto=format&fit=crop&q=80',
                    'customer_fb_id' => 'fb_cust_11094',
                    'source' => 'messenger',
                    'status' => 'shipped',
                    'payment_status' => 'paid',
                    'subtotal' => $sonyProduct->price,
                    'shipping_fee' => 4.50,
                    'discount_amount' => 15.00,
                    'total_amount' => ($sonyProduct->price + 4.50) - 15.00,
                    'notes' => 'Shipped via Virak Buntham express tracking #VET-982341.',
                ]
            );

            OrderItem::firstOrCreate(
                ['order_id' => $order3->id, 'product_id' => $sonyProduct->id],
                [
                    'product_name' => $sonyProduct->name,
                    'product_sku' => $sonyProduct->sku,
                    'unit_price' => $sonyProduct->price,
                    'quantity' => 1,
                    'subtotal' => $sonyProduct->price,
                ]
            );

            if ($order3->messages()->count() === 0) {
                OrderMessage::create([
                    'order_id' => $order3->id,
                    'sender_type' => 'customer',
                    'sender_name' => 'Rathana Meng',
                    'type' => 'messenger',
                    'message' => 'Hi admin, do you offer express delivery to Siem Reap for the Sony WH-1000XM5?',
                    'created_at' => Carbon::now()->subDays(1)->subHours(4),
                ]);
                OrderMessage::create([
                    'order_id' => $order3->id,
                    'sender_type' => 'page_agent',
                    'sender_name' => 'TechNest Support',
                    'type' => 'messenger',
                    'message' => 'Hello Rathana! Yes, we deliver daily via Virak Buntham. Delivery takes 24 hours. Here is our ABA QR code for prepayment.',
                    'created_at' => Carbon::now()->subDays(1)->subHours(3)->subMinutes(30),
                ]);
                OrderMessage::create([
                    'order_id' => $order3->id,
                    'sender_type' => 'customer',
                    'sender_name' => 'Rathana Meng',
                    'type' => 'messenger',
                    'message' => 'Paid $389.49 via ABA bank transfer! Confirmed shipping address: Siem Reap City near Night Market. Tel 077 112 233.',
                    'created_at' => Carbon::now()->subDays(1)->subHours(3),
                ]);
                OrderMessage::create([
                    'order_id' => $order3->id,
                    'sender_type' => 'page_agent',
                    'sender_name' => 'TechNest Support',
                    'type' => 'messenger',
                    'message' => 'Payment received in full! Parcel handed to courier. Tracking number: VET-982341. Driver will call you tomorrow upon arrival.',
                    'created_at' => Carbon::now()->subHours(18),
                ]);
            }
        }

        // 6. Seed Closed Orders (Success & Fail)
        // Order 4: Success (Bella Boutique)
        $sunProduct = Product::where('sku', 'FSH-SUN-003')->first();
        if ($sunProduct) {
            $order4 = Order::updateOrCreate(
                ['order_number' => 'FB-ORD-6100'],
                [
                    'facebook_page_id' => $fashionPage->id,
                    'customer_name' => 'Bopha Keo',
                    'customer_phone' => '010 445 566',
                    'customer_address' => '#88, Preah Norodom Blvd, Daun Penh, Phnom Penh',
                    'customer_avatar' => 'https://images.unsplash.com/photo-1517841905240-472988babdf9?w=150&auto=format&fit=crop&q=80',
                    'customer_fb_id' => 'fb_cust_44021',
                    'source' => 'livestream',
                    'status' => 'success',
                    'payment_status' => 'paid',
                    'subtotal' => $sunProduct->price,
                    'shipping_fee' => 0.00,
                    'discount_amount' => 5.00,
                    'total_amount' => $sunProduct->price - 5.00,
                    'notes' => 'Livestream promo discount applied.',
                    'closed_at' => Carbon::now()->subDays(2),
                ]
            );

            OrderItem::firstOrCreate(
                ['order_id' => $order4->id, 'product_id' => $sunProduct->id],
                [
                    'product_name' => $sunProduct->name,
                    'product_sku' => $sunProduct->sku,
                    'unit_price' => $sunProduct->price,
                    'quantity' => 1,
                    'subtotal' => $sunProduct->price,
                ]
            );

            if ($order4->messages()->count() === 0) {
                OrderMessage::create([
                    'order_id' => $order4->id,
                    'sender_type' => 'customer',
                    'sender_name' => 'Bopha Keo',
                    'type' => 'comment',
                    'message' => 'Comment on Live: "CF Tortoiseshell Sunglasses #SUN01 + Tel 010 445 566"',
                    'created_at' => Carbon::now()->subDays(3),
                ]);
                OrderMessage::create([
                    'order_id' => $order4->id,
                    'sender_type' => 'page_agent',
                    'sender_name' => 'Bella Fashion Live',
                    'type' => 'messenger',
                    'message' => 'Congratulations Bopha! Won live auction deal for $80. Package delivered yesterday via driver Sok.',
                    'created_at' => Carbon::now()->subDays(2)->subHours(10),
                ]);
                OrderMessage::create([
                    'order_id' => $order4->id,
                    'sender_type' => 'customer',
                    'sender_name' => 'Bopha Keo',
                    'type' => 'messenger',
                    'message' => 'Received the package! The sunglasses fit amazingly well and the leather pouch is gorgeous. Thank you!',
                    'created_at' => Carbon::now()->subDays(2)->subHours(2),
                ]);
                OrderMessage::create([
                    'order_id' => $order4->id,
                    'sender_type' => 'system',
                    'sender_name' => 'StockPilot Order System',
                    'type' => 'system_event',
                    'message' => 'Order marked as Delivered & Closed (Success).',
                    'created_at' => Carbon::now()->subDays(2),
                ]);
            }
        }

        // Order 5: Fail (TechNest Electronics)
        $keyProduct = Product::where('sku', 'ELC-KEY-002')->first();
        if ($keyProduct) {
            $order5 = Order::updateOrCreate(
                ['order_number' => 'FB-ORD-4210'],
                [
                    'facebook_page_id' => $techPage->id,
                    'customer_name' => 'Vireak Chea',
                    'customer_phone' => '089 998 877',
                    'customer_address' => 'St 102, Battambang Province',
                    'customer_avatar' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=150&auto=format&fit=crop&q=80',
                    'customer_fb_id' => 'fb_cust_33109',
                    'source' => 'messenger',
                    'status' => 'fail',
                    'payment_status' => 'unpaid',
                    'subtotal' => $keyProduct->price,
                    'shipping_fee' => 3.00,
                    'discount_amount' => 0.00,
                    'total_amount' => $keyProduct->price + 3.00,
                    'notes' => 'Cancelled before shipment.',
                    'fail_reason' => 'Customer requested cancellation: bought from a local physical shop instead.',
                    'closed_at' => Carbon::now()->subDays(4),
                ]
            );

            OrderItem::firstOrCreate(
                ['order_id' => $order5->id, 'product_id' => $keyProduct->id],
                [
                    'product_name' => $keyProduct->name,
                    'product_sku' => $keyProduct->sku,
                    'unit_price' => $keyProduct->price,
                    'quantity' => 1,
                    'subtotal' => $keyProduct->price,
                ]
            );

            if ($order5->messages()->count() === 0) {
                OrderMessage::create([
                    'order_id' => $order5->id,
                    'sender_type' => 'customer',
                    'sender_name' => 'Vireak Chea',
                    'type' => 'messenger',
                    'message' => 'Hi admin, please hold my order for the Mechanical Keyboard Pro.',
                    'created_at' => Carbon::now()->subDays(4)->subHours(6),
                ]);
                OrderMessage::create([
                    'order_id' => $order5->id,
                    'sender_type' => 'customer',
                    'sender_name' => 'Vireak Chea',
                    'type' => 'messenger',
                    'message' => 'Sorry, I need to cancel this order as I already bought a replacement locally today in Battambang.',
                    'created_at' => Carbon::now()->subDays(4)->subHours(5),
                ]);
                OrderMessage::create([
                    'order_id' => $order5->id,
                    'sender_type' => 'page_agent',
                    'sender_name' => 'TechNest Support',
                    'type' => 'messenger',
                    'message' => 'Understood Vireak! We have cancelled your order. Hope to serve you next time!',
                    'created_at' => Carbon::now()->subDays(4)->subHours(4)->subMinutes(30),
                ]);
                OrderMessage::create([
                    'order_id' => $order5->id,
                    'sender_type' => 'system',
                    'sender_name' => 'StockPilot Order System',
                    'type' => 'system_event',
                    'message' => 'Order marked as Failed/Cancelled. Reason: Customer requested cancellation: bought from a local physical shop instead.',
                    'created_at' => Carbon::now()->subDays(4)->subHours(4),
                ]);
            }
        }
    }
}
