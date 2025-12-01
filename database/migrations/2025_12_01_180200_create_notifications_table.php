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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('message');
            $table->enum('type', [
                'booking_created',
                'booking_confirmed',
                'booking_cancelled',
                'booking_rescheduled',
                'booking_reminder',
                'new_review',
                'new_message',
                'payment_received',
                'payment_failed',
                'system_announcement'
            ]);
            $table->json('data')->nullable(); // Additional data (e.g., booking_id, doctor_id)
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            // Indexes for efficient querying
            $table->index(['user_id', 'is_read', 'created_at']);
            $table->index(['user_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
