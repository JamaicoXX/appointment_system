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
        Schema::create('medical_records', function (Blueprint $table) {
            $table->id(); // Primary Key

            $table->foreignId('patient_id')
                  ->constrained('patients')
                  ->onDelete('cascade');

            $table->string('doctor_name')->nullable();

            $table->text('chief_complaint')->nullable();
            $table->text('findings')->nullable();
            $table->text('diagnosis')->nullable();
            $table->text('treatment_plan')->nullable();
            $table->text('prescription')->nullable();

            // Can store JSON array of file paths
            $table->json('attachments')->nullable();
            $table->date('follow_up_date')->nullable();
            $table->boolean('archived')->default(false);
            $table->timestamps();

            // Indexes
            $table->index(['patient_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_records');
    }
};
