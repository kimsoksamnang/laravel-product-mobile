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
        Schema::create('facebook_pages', function (Blueprint $table) {
            $table->id();
            $table->string('page_id')->unique(); // Facebook Page ID
            $table->string('name');
            $table->string('category')->nullable();
            $table->string('avatar_url')->nullable();
            $table->string('cover_url')->nullable();
            $table->text('access_token')->nullable();
            $table->unsignedInteger('followers_count')->default(0);
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('about')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('facebook_page_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('facebook_page_id')->constrained()->cascadeOnDelete();
            $table->string('role')->default('owner'); // owner, admin, moderator, sales_agent
            $table->timestamps();

            $table->unique(['user_id', 'facebook_page_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facebook_page_user');
        Schema::dropIfExists('facebook_pages');
    }
};
