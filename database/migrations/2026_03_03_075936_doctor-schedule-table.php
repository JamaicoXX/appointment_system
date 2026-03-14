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
        Schema::create('doctor_schedules', function (Blueprint $table) {
            $table->id(); // Primary Key
            $table->enum('day_of_week', [
                'Sunday',
                'Monday',
                'Tuesday',
                'Wednesday',
                'Thursday',
                'Friday',
                'Saturday'
            ]);
            $table->time('start_time');
            $table->time('end_time');
            $table->unsignedInteger('slot_limit')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['day_of_week']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::dropIfExists('doctor_schedules');
    }
};
