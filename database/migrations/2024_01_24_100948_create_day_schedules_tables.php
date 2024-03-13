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
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->string('class')->unique();
            $table->string('hash')->nullable();
            $table->timestamp('lastUpdated')->nullable();
        });

        Schema::create('day_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->constrained('schedules');
            $table->string('day');
            $table->time('start_time');
            $table->time('end_time');
            $table->text('json');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('day_schedules');
        Schema::dropIfExists('schedules');
    }
};
