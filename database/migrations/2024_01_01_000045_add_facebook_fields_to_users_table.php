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
        Schema::table('users', function (Blueprint $table) {
            $table->string('facebook_user_id')->nullable()->after('id');
            $table->string('avatar_url')->nullable()->after('email');
            $table->string('phone')->nullable()->after('avatar_url');
            $table->string('role')->default('Shop Owner')->after('phone');
            $table->string('fb_profile_url')->nullable()->after('role');
            $table->text('bio')->nullable()->after('fb_profile_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'facebook_user_id',
                'avatar_url',
                'phone',
                'role',
                'fb_profile_url',
                'bio',
            ]);
        });
    }
};
