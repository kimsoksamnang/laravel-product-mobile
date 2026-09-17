<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_catalog_screen_renders_successfully(): void
    {
        $response = $this->get('/products');
        $response->assertStatus(200);
        $response->assertSee('StockPilot');
        $response->assertSee('Sony WH-1000XM5');
    }

    public function test_filter_by_category_and_status(): void
    {
        $response = $this->get('/products?category=electronics&stock_status=in_stock');
        $response->assertStatus(200);
        $response->assertSee('Sony WH-1000XM5');

        $responseLow = $this->get('/products?stock_status=low_stock');
        $responseLow->assertStatus(200);
        $responseLow->assertSee('Low Stock');
    }

    public function test_can_create_new_product(): void
    {
        $category = Category::first();

        $response = $this->post('/products', [
            'name' => 'Test Wireless Mouse',
            'category_id' => $category->id,
            'sku' => 'TEST-MSE-999',
            'barcode' => '1234567890123',
            'description' => 'Ergonomic test mouse',
            'price' => 45.00,
            'cost_price' => 20.00,
            'stock_quantity' => 15,
            'low_stock_threshold' => 5,
            'status' => 'active',
        ]);

        $response->assertRedirect('/products');
        $this->assertDatabaseHas('products', [
            'sku' => 'TEST-MSE-999',
            'stock_quantity' => 15,
        ]);
        $this->assertDatabaseHas('stock_movements', [
            'type' => 'in',
            'quantity' => 15,
        ]);
    }

    public function test_product_detail_screen_renders(): void
    {
        $product = Product::first();
        $response = $this->get("/products/{$product->id}");
        $response->assertStatus(200);
        $response->assertSee($product->name);
        $response->assertSee($product->sku);
    }

    public function test_quick_stock_adjustment_via_ajax(): void
    {
        $product = Product::where('stock_quantity', '>', 5)->first();
        $initialStock = $product->stock_quantity;

        $response = $this->postJson("/products/{$product->id}/adjust-stock", [
            'mode' => 'increment',
            'amount' => 5,
            'reason' => 'Test Shipment Arrival',
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'new_stock' => $initialStock + 5,
                 ]);

        $product->refresh();
        $this->assertEquals($initialStock + 5, $product->stock_quantity);

        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'type' => 'in',
            'quantity' => 5,
            'reason' => 'Test Shipment Arrival',
        ]);
    }

    public function test_dashboard_metrics_screen_renders(): void
    {
        $response = $this->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Inventory Metrics');
        $response->assertSee('Inventory Value');
    }

    public function test_categories_screen_renders(): void
    {
        $response = $this->get('/categories');
        $response->assertStatus(200);
        $response->assertSee('Categories');
        $response->assertSee('Electronics');
    }

    public function test_csv_export_streams_file(): void
    {
        $response = $this->get('/products/export');
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=utf-8');
    }
}
