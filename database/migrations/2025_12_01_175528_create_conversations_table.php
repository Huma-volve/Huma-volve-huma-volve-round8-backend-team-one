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
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('doctor_id')->constrained('doctors')->onDelete('cascade');
            $table->timestamp('last_message_at')->nullable();
            $table->boolean('is_archived_by_patient')->default(false);
            $table->boolean('is_archived_by_doctor')->default(false);
            $table->boolean('is_favorited_by_patient')->default(false);
            $table->boolean('is_favorited_by_doctor')->default(false);
            $table->timestamps();

            // Unique constraint to prevent duplicate conversations
            $table->unique(['patient_id', 'doctor_id']);
            $table->index(['patient_id', 'last_message_at']);
            $table->index(['doctor_id', 'last_message_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
