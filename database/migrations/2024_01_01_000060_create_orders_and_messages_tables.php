<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facebook_page_id')->nullable()->constrained('facebook_pages')->nullOnDelete();
            $table->string('order_number')->unique();
            $table->string('customer_name');
            $table->string('customer_phone')->nullable();
            $table->text('customer_address')->nullable();
            $table->string('customer_avatar')->nullable();
            $table->string('customer_fb_id')->nullable();
            $table->string('source')->default('messenger'); // messenger, comment, livestream, manual
            $table->string('status')->default('pending'); // pending, confirmed, processing, shipped, success, fail
            $table->string('payment_status')->default('cod'); // cod, paid, unpaid
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('shipping_fee', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->text('fail_reason')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->string('product_name');
            $table->string('product_sku')->nullable();
            $table->decimal('unit_price', 10, 2)->default(0);
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('order_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->string('sender_type')->default('customer'); // customer, page_agent, system
            $table->string('sender_name');
            $table->string('sender_avatar')->nullable();
            $table->string('type')->default('messenger'); // messenger, comment, system_event
            $table->text('message');
            $table->string('attachment_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_messages');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
