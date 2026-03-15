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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Service name (e.g., Tooth Extraction)
            $table->text('description')->nullable(); // Service details
            $table->decimal('price', 10, 2)->nullable(); // Service price
            $table->decimal('discount_price', 10, 2)->nullable(); // Discount price
            $table->integer('duration')->nullable(); // Duration in minutes
            $table->boolean('archived')->default(false); // If service is available
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
