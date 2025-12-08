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

        /**
         * ----------------------------------------------------------
         * USERS TABLE INDEXES
         * ----------------------------------------------------------
         *
         * user_type + status:
         *   - Improves performance for admin dashboards or APIs that filter
         *     users by their role (doctor, patient, admin...) AND their status
         *     (active / non-active / pending).
         *   - Speeds up queries like:
         *         SELECT * FROM users WHERE user_type = 'doctor' AND status = 'active';
         *
         * name:
         *   - Speeds up search operations when searching users by name
         *     (LIKE queries still benefit from left-anchored indexes).
         *   - Useful in search bars, admin listings, or autocomplete features.
         */
        Schema::table('users', function (Blueprint $table) {

            $table->index(['user_type', 'status']);
            $table->index('name');
        });

        /**
         * ----------------------------------------------------------
         * DOCTOR_PROFILES TABLE INDEXES
         * ----------------------------------------------------------
         *
         * is_approved + specialty_id:
         *   - Optimizes queries retrieving doctors grouped or filtered by specialty
         *     ONLY when approved doctors are shown.
         *   - Common query example:
         *         SELECT * FROM doctor_profiles
         *         WHERE is_approved = 1 AND specialty_id = 5;
         *
         * is_approved + rating_avg:
         *   - Helps when sorting or filtering doctors by rating,
         *     but only among approved doctors.
         *   - Example:
         *         SELECT * FROM doctor_profiles
         *         WHERE is_approved = 1 ORDER BY rating_avg DESC;
         *
         * is_approved + session_price:
         *   - Useful when filtering or sorting by price range.
         *   - Example:
         *         SELECT * FROM doctor_profiles
         *         WHERE is_approved = 1 AND session_price <= 300;
         */
        Schema::table('doctor_profiles', function (Blueprint $table) {

            $table->index(['is_approved', 'specialty_id']);
            $table->index(['is_approved', 'rating_avg']);
            $table->index(['is_approved', 'session_price']);
        });
        /**
         * ----------------------------------------------------------
         * BOOKINGS TABLE INDEXES
         * ----------------------------------------------------------
         *
         * doctor_id + status + appointment_date:
         *   - Improves performance for doctor schedule queries.
         *   - Used when showing upcoming bookings for a specific doctor.
         *   - Example:
         *         SELECT * FROM bookings
         *         WHERE doctor_id = 10 AND status = 'confirmed'
         *         ORDER BY appointment_date;
         *
         * patient_id + status + appointment_date:
         *   - Optimizes patient booking history and upcoming appointment queries.
         *   - Example:
         *         SELECT * FROM bookings
         *         WHERE patient_id = 99 AND status = 'completed';
         *
         * appointment_date + status:
         *   - Useful for admin analytics showing overall bookings on specific days.
         *   - Good for daily dashboards, calendars, statistics, etc.
         *   - Example:
         *         SELECT COUNT(*) FROM bookings
         *         WHERE appointment_date = '2025-01-05' AND status = 'confirmed';
         */
        Schema::table('bookings', function (Blueprint $table) {

            $table->index(['doctor_id', 'status', 'appointment_date']);
            $table->index(['patient_id', 'status', 'appointment_date']);
            $table->index(['appointment_date', 'status']);

        });
        /**
         * ----------------------------------------------------------
         * AVAILABILITY_SLOTS TABLE INDEX
         * ----------------------------------------------------------
         *
         * Composite Index: date + is_booked + is_active
         *
         * This index is designed to optimize availability searching for doctors.
         * It significantly speeds up queries that filter slots based on:
         *   - a specific date
         *   - whether the slot is booked or not
         *   - whether the slot is active/visible
         */
        Schema::table('availability_slots', function (Blueprint $table) {
            $table->index(['date', 'is_booked', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tables', function (Blueprint $table) {
            //
        });
    }
};
