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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id(); // Primary Key

            $table->foreignId('patient_id')
                  ->constrained('patients')
                  ->onDelete('cascade');

            $table->date('appointment_date');
            $table->time('appointment_time');

            $table->enum('status', [
                'pending',
                'confirmed',
                'ongoing',
                'done',
                'cancelled'
            ])->default('pending');

            $table->enum('payment_status', [
                'unpaid',
                'pending_review',
                'paid',
                'rejected'
            ])->default('unpaid');

            $table->text('notes')->nullable();

            $table->timestamps();

            // indexes
            $table->index(['patient_id', 'appointment_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
