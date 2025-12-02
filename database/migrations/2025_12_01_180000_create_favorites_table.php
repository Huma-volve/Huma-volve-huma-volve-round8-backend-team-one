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
        Schema::create('favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients_profiles')->onDelete('cascade');
            $table->foreignId('doctor_id')->constrained('doctors_profiles')->onDelete('cascade');
            $table->timestamps();

            // Unique constraint to prevent duplicate favorites
            $table->unique(['patient_id', 'doctor_id']);
            $table->index(['patient_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('favorites');
    }
};
