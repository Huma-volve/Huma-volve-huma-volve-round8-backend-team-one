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
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('specialty_id')->constrained('specialties')->onDelete('restrict');
            $table->string('license_number', 50)->unique();
            $table->text('bio')->nullable();
            $table->decimal('session_price', 8, 2);
            $table->string('clinic_address', 255);
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->decimal('rating_avg', 3, 2)->default(0);
            $table->integer('total_reviews')->default(0);
            $table->boolean('is_approved')->default(false);
            $table->string('temporary_password')->nullable(); // For admin-created accounts
            $table->boolean('password_changed')->default(false); // Track if temp password was changed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};
