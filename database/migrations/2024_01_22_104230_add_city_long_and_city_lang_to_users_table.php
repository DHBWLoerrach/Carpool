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
            $table->string('cityLat')->nullable();
            $table->string('cityLon')->nullable();
        });
        DB::statement('ALTER TABLE users CHANGE COLUMN cityLat cityLat VARCHAR(255) AFTER city');
        DB::statement('ALTER TABLE users CHANGE COLUMN cityLon cityLon VARCHAR(255) AFTER cityLat');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('cityLat');
            $table->dropColumn('cityLon');
        });
    }
};
