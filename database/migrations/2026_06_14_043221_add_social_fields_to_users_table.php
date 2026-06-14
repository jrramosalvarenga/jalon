<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('google_id')->nullable()->unique()->after('phone');
            $table->string('facebook_id')->nullable()->unique()->after('google_id');
            $table->text('facebook_token')->nullable()->after('facebook_id');
            $table->string('avatar')->nullable()->after('facebook_token');
        });

        DB::statement('ALTER TABLE users ALTER COLUMN password DROP NOT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['google_id', 'facebook_id', 'facebook_token', 'avatar']);
        });

        DB::statement('ALTER TABLE users ALTER COLUMN password SET NOT NULL');
    }
};
