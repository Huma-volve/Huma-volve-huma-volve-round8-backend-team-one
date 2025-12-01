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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->onDelete('cascade');
            $table->string('external_id', 255)->nullable(); // PayPal/Stripe transaction ID
            $table->decimal('amount', 8, 2);
            $table->enum('type', ['payment', 'refund']);
            $table->enum('status', ['success', 'failed', 'pending']);
            $table->enum('gateway', ['stripe', 'paypal', 'cash']);
            $table->json('payload')->nullable(); // Full response from payment gateway
            $table->string('currency', 3)->default('USD');
            $table->text('failure_reason')->nullable(); // Reason if payment failed
            $table->timestamps();

            // Index for reporting
            $table->index(['booking_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
