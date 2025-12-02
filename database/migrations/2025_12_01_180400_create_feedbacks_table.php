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
        Schema::create('feedbacks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients_profiles')->onDelete('cascade');
            $table->foreignId('booking_id')->constrained('bookings')->onDelete('cascade');
            $table->foreignId('doctor_id')->constrained('doctors_profiles')->onDelete('cascade');
            $table->text('feedback_text');
            $table->integer('rating')->unsigned(); // 1-5 rating for session quality
            $table->timestamps();

            // Index for doctor to view feedbacks
            $table->index(['doctor_id', 'created_at']);
            $table->index(['booking_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedbacks');
    }
};
