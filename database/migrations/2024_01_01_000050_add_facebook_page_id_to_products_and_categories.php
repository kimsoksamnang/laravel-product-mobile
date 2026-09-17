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
        Schema::table('categories', function (Blueprint $table) {
            $table->foreignId('facebook_page_id')->nullable()->after('id')->constrained('facebook_pages')->nullOnDelete();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('facebook_page_id')->nullable()->after('id')->constrained('facebook_pages')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropConstrainedForeignId('facebook_page_id');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropConstrainedForeignId('facebook_page_id');
        });
    }
};
