<?php

namespace Tests\Feature;

use App\Models\FacebookPage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $merchantUser;
    protected FacebookPage $page;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->adminUser = User::where('email', 'samnang@example.com')->first();
        $this->merchantUser = User::where('email', 'dara.sok@example.com')->first();
        $this->page = FacebookPage::first();
    }

    public function test_admin_dashboard_accessible_by_system_admin(): void
    {
        // Admin access
        $response = $this->withSession(['acting_user_id' => $this->adminUser->id])
                         ->get('/admin');

        $response->assertStatus(200);
        $response->assertSee('System Admin Console');
        $response->assertSee('Merchants & Users', false);
        $response->assertSee('AI Active');
    }

    public function test_admin_dashboard_denies_non_admin_merchant(): void
    {
        // Merchant access without admin role
        $response = $this->withSession(['acting_user_id' => $this->merchantUser->id])
                         ->get('/admin');

        $response->assertRedirect(route('products.index'));
        $response->assertSessionHas('error');
    }

    public function test_admin_can_view_users_list_and_filter(): void
    {
        $response = $this->withSession(['acting_user_id' => $this->adminUser->id])
                         ->get('/admin/users');

        $response->assertStatus(200);
        $response->assertSee('Samnang Heng');
        $response->assertSee('Dara Sok');

        // Filter by admin
        $adminFilter = $this->withSession(['acting_user_id' => $this->adminUser->id])
                            ->get('/admin/users?role_filter=admin');
        $adminFilter->assertStatus(200);
        $adminFilter->assertSee('Samnang Heng');

        // Filter by FB connected
        $fbFilter = $this->withSession(['acting_user_id' => $this->adminUser->id])
                         ->get('/admin/users?fb_filter=connected');
        $fbFilter->assertStatus(200);
        $fbFilter->assertSee('Connected');
    }

    public function test_admin_can_create_user_with_assigned_facebook_pages(): void
    {
        $response = $this->withSession(['acting_user_id' => $this->adminUser->id])
                         ->post('/admin/users', [
                             'name' => 'Vannak Ouk',
                             'email' => 'vannak@example.com',
                             'password' => 'secret123',
                             'phone' => '+855 12 900 111',
                             'role' => 'Sales Lead',
                             'is_system_admin' => 0,
                             'pages' => [$this->page->id],
                         ]);

        $response->assertRedirect('/admin/users');
        $this->assertDatabaseHas('users', [
            'email' => 'vannak@example.com',
            'name' => 'Vannak Ouk',
            'is_system_admin' => false,
        ]);

        $newUser = User::where('email', 'vannak@example.com')->first();
        $this->assertTrue($newUser->facebookPages->contains($this->page->id));
    }

    public function test_admin_can_update_user_and_toggle_admin_status(): void
    {
        $response = $this->withSession(['acting_user_id' => $this->adminUser->id])
                         ->put('/admin/users/' . $this->merchantUser->id, [
                             'name' => 'Dara Sok Super Merchant',
                             'email' => 'dara.sok@example.com',
                             'phone' => '+855 77 999 888',
                             'role' => 'Executive Merchant',
                             'is_system_admin' => 1,
                             'pages' => [$this->page->id],
                         ]);

        $response->assertRedirect('/admin/users');
        $this->merchantUser->refresh();
        $this->assertEquals('Dara Sok Super Merchant', $this->merchantUser->name);
        $this->assertTrue($this->merchantUser->is_system_admin);
        $this->assertTrue($this->merchantUser->facebookPages->contains($this->page->id));
    }

    public function test_admin_can_toggle_facebook_connection_for_user(): void
    {
        $this->assertTrue($this->adminUser->isFacebookConnected());

        // Disconnect
        $response = $this->withSession(['acting_user_id' => $this->adminUser->id])
                         ->post('/admin/users/' . $this->adminUser->id . '/toggle-facebook');
        $response->assertStatus(302);

        $this->adminUser->refresh();
        $this->assertFalse($this->adminUser->isFacebookConnected());
        $this->assertNull($this->adminUser->facebook_user_id);

        // Reconnect
        $response2 = $this->withSession(['acting_user_id' => $this->adminUser->id])
                          ->post('/admin/users/' . $this->adminUser->id . '/toggle-facebook');
        $response2->assertStatus(302);

        $this->adminUser->refresh();
        $this->assertTrue($this->adminUser->isFacebookConnected());
        $this->assertNotNull($this->adminUser->facebook_user_id);
    }

    public function test_user_can_connect_and_disconnect_facebook_from_profile(): void
    {
        $this->assertTrue($this->adminUser->isFacebookConnected());

        // Disconnect from profile
        $response = $this->withSession(['acting_user_id' => $this->adminUser->id])
                         ->post('/profile/disconnect-facebook');
        $response->assertRedirect('/profile');

        $this->adminUser->refresh();
        $this->assertFalse($this->adminUser->isFacebookConnected());

        // Connect from profile
        $connectResponse = $this->withSession(['acting_user_id' => $this->adminUser->id])
                                ->post('/profile/connect-facebook', [
                                    'facebook_user_id' => 'fb_custom_888999',
                                    'fb_profile_url' => 'https://facebook.com/samnang.pro',
                                ]);
        $connectResponse->assertRedirect('/profile');

        $this->adminUser->refresh();
        $this->assertTrue($this->adminUser->isFacebookConnected());
        $this->assertEquals('fb_custom_888999', $this->adminUser->facebook_user_id);
    }

    public function test_toggle_ai_order_agent_on_facebook_page(): void
    {
        $initialState = (bool) $this->page->ai_order_agent_enabled;

        // Toggle via admin
        $adminToggle = $this->withSession(['acting_user_id' => $this->adminUser->id])
                            ->post('/admin/facebook-pages/' . $this->page->id . '/toggle-ai');
        $adminToggle->assertStatus(302);

        $this->page->refresh();
        $this->assertEquals(!$initialState, (bool) $this->page->ai_order_agent_enabled);

        // Toggle via shop view
        $shopToggle = $this->withSession(['acting_user_id' => $this->adminUser->id])
                           ->post('/facebook-pages/' . $this->page->id . '/toggle-ai');
        $shopToggle->assertStatus(302);

        $this->page->refresh();
        $this->assertEquals($initialState, (bool) $this->page->ai_order_agent_enabled);
    }

    public function test_admin_can_create_and_manage_facebook_page(): void
    {
        $uniquePageId = 'fb_pg_' . rand(1000000, 9999999);
        $response = $this->withSession(['acting_user_id' => $this->adminUser->id])
                         ->post('/admin/facebook-pages', [
                             'name' => 'Kroma & Silk Handcraft',
                             'page_id' => $uniquePageId,
                             'category' => 'Traditional Textiles',
                             'followers_count' => 8500,
                             'ai_order_agent_enabled' => 1,
                             'users' => [$this->adminUser->id, $this->merchantUser->id],
                         ]);

        $response->assertRedirect('/admin/facebook-pages');
        $this->assertDatabaseHas('facebook_pages', [
            'page_id' => $uniquePageId,
            'name' => 'Kroma & Silk Handcraft',
            'ai_order_agent_enabled' => true,
        ]);

        $createdPage = FacebookPage::where('page_id', $uniquePageId)->first();
        $this->assertCount(2, $createdPage->users);

        // Edit page
        $editResponse = $this->withSession(['acting_user_id' => $this->adminUser->id])
                             ->put('/admin/facebook-pages/' . $createdPage->id, [
                                 'name' => 'Kroma & Silk Handcraft Cambodia',
                                 'category' => 'Silk & Fashion Textiles',
                                 'followers_count' => 10200,
                                 'ai_order_agent_enabled' => 0,
                                 'users' => [$this->merchantUser->id],
                             ]);

        $editResponse->assertRedirect('/admin/facebook-pages');
        $createdPage->refresh();
        $this->assertEquals('Kroma & Silk Handcraft Cambodia', $createdPage->name);
        $this->assertFalse((bool) $createdPage->ai_order_agent_enabled);
        $this->assertCount(1, $createdPage->users);
    }
}
