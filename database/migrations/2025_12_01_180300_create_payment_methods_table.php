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
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients_profiles')->onDelete('cascade');
            $table->enum('type', ['paypal', 'stripe', 'card']);
            $table->string('provider_customer_id')->nullable(); // Stripe customer ID or PayPal email
            $table->string('last_four')->nullable(); // Last 4 digits of card
            $table->string('card_brand')->nullable(); // Visa, Mastercard, etc.
            $table->string('expiry_month', 2)->nullable();
            $table->string('expiry_year', 4)->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            // Index for quick lookup
            $table->index(['patient_id', 'is_default']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
