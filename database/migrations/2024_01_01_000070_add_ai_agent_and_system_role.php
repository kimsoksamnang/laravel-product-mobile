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
        Schema::table('facebook_pages', function (Blueprint $table) {
            $table->boolean('ai_order_agent_enabled')->default(true)->after('is_active');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_system_admin')->default(false)->after('role');
            $table->timestamp('facebook_connected_at')->nullable()->after('facebook_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_system_admin', 'facebook_connected_at']);
        });

        Schema::table('facebook_pages', function (Blueprint $table) {
            $table->dropColumn('ai_order_agent_enabled');
        });
    }
};
