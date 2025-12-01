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
        Schema::create('search_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->string('search_query');
            $table->enum('search_type', ['specialty', 'name', 'location'])->default('specialty');
            $table->decimal('latitude', 10, 8)->nullable(); // For location-based searches
            $table->decimal('longitude', 11, 8)->nullable();
            $table->timestamps();

            // Index for quick retrieval of patient's search history
            $table->index(['patient_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('search_history');
    }
};
