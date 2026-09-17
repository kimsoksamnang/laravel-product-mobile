<?php

namespace Tests\Feature;

use App\Models\FacebookPage;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FacebookCommerceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_user_profile_screen_renders_and_updates(): void
    {
        $response = $this->get('/profile');
        $response->assertStatus(200);
        $response->assertSee('User Profile');
        $response->assertSee('Samnang Heng');
        $response->assertSee('Facebook Pages');

        // Update profile
        $updateResponse = $this->put('/profile', [
            'name' => 'Samnang Heng Updated',
            'phone' => '+855 99 111 222',
            'role' => 'Master Store Admin',
            'bio' => 'Updated bio information.',
        ]);

        $updateResponse->assertRedirect('/profile');
        $this->assertDatabaseHas('users', [
            'name' => 'Samnang Heng Updated',
            'phone' => '+855 99 111 222',
            'role' => 'Master Store Admin',
        ]);
    }

    public function test_facebook_pages_screen_renders_and_switches_active_page(): void
    {
        $response = $this->get('/facebook-pages');
        $response->assertStatus(200);
        $response->assertSee('Facebook Pages');
        $response->assertSee('Bella Fashion Boutique');
        $response->assertSee('TechNest Electronics');

        $page = FacebookPage::first();

        // Switch active page
        $switchResponse = $this->post("/facebook-pages/switch/{$page->id}");
        $switchResponse->assertSessionHas('active_facebook_page_id', $page->id);

        // Switch to all pages
        $switchAllResponse = $this->post('/facebook-pages/switch/all');
        $switchAllResponse->assertSessionHas('active_facebook_page_id', 'all');

        // Connect new page
        $createResponse = $this->post('/facebook-pages', [
            'name' => 'Angkor Organic Coffee',
            'page_id' => 'fb_page_999888777',
            'category' => 'Food & Beverage',
            'followers_count' => 15000,
            'phone' => '+855 15 999 888',
        ]);

        $createResponse->assertRedirect('/facebook-pages');
        $this->assertDatabaseHas('facebook_pages', [
            'name' => 'Angkor Organic Coffee',
            'page_id' => 'fb_page_999888777',
        ]);
    }

    public function test_open_orders_screen_renders_and_filters_by_status(): void
    {
        $response = $this->get('/orders/open');
        $response->assertStatus(200);
        $response->assertSee('Open Orders');
        $response->assertSee('Sokha Chan');

        // Filter by pending
        $pendingResponse = $this->get('/orders/open?status=pending');
        $pendingResponse->assertStatus(200);
        $pendingResponse->assertSee('Pending Review');

        // Filter by confirmed
        $confirmedResponse = $this->get('/orders/open?status=confirmed');
        $confirmedResponse->assertStatus(200);
        $confirmedResponse->assertSee('Davy Pich');
    }

    public function test_closed_orders_screen_renders_and_filters_success_and_fail(): void
    {
        $response = $this->get('/orders/closed');
        $response->assertStatus(200);
        $response->assertSee('Closed Orders');
        $response->assertSee('Delivered Revenue');
        $response->assertSee('Success Rate');

        // Filter success
        $successResponse = $this->get('/orders/closed?status=success');
        $successResponse->assertStatus(200);
        $successResponse->assertSee('Bopha Keo');

        // Filter fail across pages
        $failResponse = $this->withSession(['active_facebook_page_id' => 'all'])->get('/orders/closed?status=fail');
        $failResponse->assertStatus(200);
        $failResponse->assertSee('Vireak Chea');
        $failResponse->assertSee('Reason for failure:');
    }

    public function test_order_show_screen_displays_details_and_facebook_chat_history(): void
    {
        $order = Order::with('messages')->first();
        $this->assertNotNull($order);

        $response = $this->get("/orders/{$order->id}");
        $response->assertStatus(200);
        $response->assertSee($order->customer_name);
        $response->assertSee('Facebook Conversation History');

        if ($order->messages->isNotEmpty()) {
            $response->assertSee($order->messages->first()->message);
        }
    }

    public function test_order_status_transition_and_adding_chat_message(): void
    {
        $order = Order::where('status', 'pending')->first();
        $this->assertNotNull($order);

        // Advance to confirmed
        $confirmResponse = $this->post("/orders/{$order->id}/status", [
            'status' => 'confirmed',
            'status_note' => 'Customer confirmed via call',
        ]);
        $confirmResponse->assertRedirect();
        $order->refresh();
        $this->assertEquals('confirmed', $order->status);

        // Mark as fail with reason
        $failResponse = $this->post("/orders/{$order->id}/status", [
            'status' => 'fail',
            'fail_reason' => 'Customer unreachable after 3 attempts',
        ]);
        $failResponse->assertRedirect();
        $order->refresh();
        $this->assertEquals('fail', $order->status);
        $this->assertEquals('Customer unreachable after 3 attempts', $order->fail_reason);
        $this->assertNotNull($order->closed_at);

        // Add chat reply
        $msgResponse = $this->post("/orders/{$order->id}/messages", [
            'message' => 'Hello, we attempted delivery today.',
            'sender_type' => 'page_agent',
        ]);
        $msgResponse->assertRedirect();
        $this->assertDatabaseHas('order_messages', [
            'order_id' => $order->id,
            'message' => 'Hello, we attempted delivery today.',
            'sender_type' => 'page_agent',
        ]);
    }

    public function test_create_new_order_with_items_and_initial_message(): void
    {
        $page = FacebookPage::first();
        $product = Product::first();

        $response = $this->post('/orders', [
            'facebook_page_id' => $page->id,
            'customer_name' => 'Chanthy Ouk',
            'customer_phone' => '092 123 456',
            'customer_address' => '#55, St 63, Boeung Keng Kang 1, Phnom Penh',
            'source' => 'messenger',
            'payment_status' => 'cod',
            'shipping_fee' => 2.50,
            'discount_amount' => 5.00,
            'initial_message' => 'Hello, I want to place an order from your post!',
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                ],
            ],
        ]);

        $order = Order::where('customer_name', 'Chanthy Ouk')->first();
        $this->assertNotNull($order);
        $response->assertRedirect("/orders/{$order->id}");

        $this->assertDatabaseHas('orders', [
            'customer_name' => 'Chanthy Ouk',
            'facebook_page_id' => $page->id,
            'status' => 'pending',
            'source' => 'messenger',
        ]);

        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $this->assertDatabaseHas('order_messages', [
            'order_id' => $order->id,
            'message' => 'Hello, I want to place an order from your post!',
            'sender_type' => 'customer',
        ]);
    }
}
