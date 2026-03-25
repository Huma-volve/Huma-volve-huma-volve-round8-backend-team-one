<?php

namespace Tests\Feature\Doctor;

use App\Models\Booking;
use App\Models\DoctorProfile;
use App\Models\PatientProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DoctorBookingPanelTest extends TestCase
{
    use RefreshDatabase;

    protected $doctorUser;

    protected $doctorProfile;

    protected $patientUser;

    protected $patientProfile;

    protected function setUp(): void
    {
        parent::setUp();

        // Create Doctor
        $this->doctorUser = User::factory()->create(['user_type' => 'doctor']);
        $this->doctorProfile = DoctorProfile::factory()->create(['user_id' => $this->doctorUser->id]);

        // Create Patient
        $this->patientUser = User::factory()->create(['user_type' => 'patient']);
        $this->patientProfile = PatientProfile::factory()->create(['user_id' => $this->patientUser->id]);
    }

    public function test_doctor_can_view_bookings_list()
    {
        $booking = Booking::factory()->create([
            'doctor_id' => $this->doctorProfile->id,
            'patient_id' => $this->patientProfile->id,
            'status' => 'pending',
            'appointment_date' => now()->addDay(),
            'appointment_time' => '10:00:00',
        ]);

        $response = $this->actingAs($this->doctorUser)->get(route('doctor.bookings.index'));

        $response->assertStatus(200);
        $response->assertSee($this->patientUser->name);
        $response->assertSee('Pending');
    }

    public function test_doctor_can_filter_bookings()
    {
        Booking::factory()->create([
            'doctor_id' => $this->doctorProfile->id,
            'patient_id' => $this->patientProfile->id,
            'status' => 'confirmed',
            'appointment_date' => now()->addDay(),
        ]);

        $response = $this->actingAs($this->doctorUser)->get(route('doctor.bookings.index', ['status' => 'confirmed']));
        $response->assertStatus(200);
        $response->assertSee('Confirmed');

        $response = $this->actingAs($this->doctorUser)->get(route('doctor.bookings.index', ['status' => 'cancelled']));
        $response->assertStatus(200);
        // $response->assertDontSee('Confirmed'); // 'Confirmed' is in the dropdown
        $response->assertViewHas('bookings', function ($bookings) {
            return $bookings->count() === 0;
        });
    }

    public function test_doctor_can_view_booking_details()
    {
        $booking = Booking::factory()->create([
            'doctor_id' => $this->doctorProfile->id,
            'patient_id' => $this->patientProfile->id,
        ]);

        $response = $this->actingAs($this->doctorUser)->get(route('doctor.bookings.show', $booking));

        $response->assertStatus(200);
        $response->assertSee($this->patientUser->name);
        $response->assertSee($this->patientUser->email);
    }

    public function test_doctor_can_cancel_booking()
    {
        $booking = Booking::factory()->create([
            'doctor_id' => $this->doctorProfile->id,
            'patient_id' => $this->patientProfile->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->doctorUser)->post(route('doctor.bookings.cancel', $booking), [
            'cancellation_reason' => 'Doctor unavailable',
        ]);

        $response->assertRedirect(route('doctor.bookings.show', $booking));

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => 'cancelled',
            'cancellation_reason' => 'Doctor unavailable',
            'cancelled_by' => $this->doctorUser->id,
        ]);
    }

    public function test_doctor_can_reschedule_booking()
    {
        $booking = Booking::factory()->create([
            'doctor_id' => $this->doctorProfile->id,
            'patient_id' => $this->patientProfile->id,
            'status' => 'confirmed',
            'appointment_date' => now()->addDay()->format('Y-m-d'),
            'appointment_time' => '10:00:00',
        ]);

        $newDate = now()->addDays(2)->format('Y-m-d');
        $newTime = '11:00'; // Assuming this format matches controller processing

        $response = $this->actingAs($this->doctorUser)->post(route('doctor.bookings.reschedule', $booking), [
            'appointment_date' => $newDate,
            'appointment_time' => $newTime,
        ]);

        $response->assertRedirect(route('doctor.bookings.show', $booking));

        $updatedBooking = $booking->fresh();

        $this->assertEquals($newDate, $updatedBooking->appointment_date->format('Y-m-d'));
        $this->assertEquals('rescheduled', $updatedBooking->status);
    }

    public function test_doctor_cannot_view_other_doctors_bookings()
    {
        $otherDoctor = User::factory()->create(['user_type' => 'doctor']);
        $otherDoctorProfile = DoctorProfile::factory()->create(['user_id' => $otherDoctor->id]);

        $booking = Booking::factory()->create([
            'doctor_id' => $otherDoctorProfile->id,
            'patient_id' => $this->patientProfile->id,
        ]);

        $response = $this->actingAs($this->doctorUser)->get(route('doctor.bookings.show', $booking));

        $response->assertStatus(403);
    }

    public function test_doctor_can_complete_todays_paid_booking()
    {
        $booking = Booking::factory()->create([
            'doctor_id' => $this->doctorProfile->id,
            'patient_id' => $this->patientProfile->id,
            'status' => 'confirmed',
            'appointment_date' => now()->format('Y-m-d'), // Today
            'appointment_time' => '10:00:00',
            'payment_status' => 'paid',
        ]);

        $response = $this->actingAs($this->doctorUser)->post(route('doctor.bookings.complete', $booking));

        $response->assertRedirect();
        $this->assertEquals('completed', $booking->fresh()->status);
    }

    public function test_doctor_cannot_complete_unpaid_booking()
    {
        $booking = Booking::factory()->create([
            'doctor_id' => $this->doctorProfile->id,
            'patient_id' => $this->patientProfile->id,
            'status' => 'confirmed',
            'appointment_date' => now()->format('Y-m-d'), // Today
            'appointment_time' => '10:00:00',
            'payment_status' => 'unpaid',
        ]);

        $response = $this->actingAs($this->doctorUser)->post(route('doctor.bookings.complete', $booking));

        $response->assertRedirect();
        $response->assertSessionHas('error'); // Should have error message
        $this->assertNotEquals('completed', $booking->fresh()->status);
    }

    public function test_doctor_cannot_complete_future_booking()
    {
        $booking = Booking::factory()->create([
            'doctor_id' => $this->doctorProfile->id,
            'patient_id' => $this->patientProfile->id,
            'status' => 'confirmed',
            'appointment_date' => now()->addDay()->format('Y-m-d'), // Tomorrow
            'appointment_time' => '10:00:00',
            'payment_status' => 'paid',
        ]);

        $response = $this->actingAs($this->doctorUser)->post(route('doctor.bookings.complete', $booking));

        $response->assertRedirect();
        $this->assertNotEquals('completed', $booking->fresh()->status);
    }

    public function test_complete_button_visibility()
    {
        // 1. Today's PAID booking -> Should see button
        $todayPaidBooking = Booking::factory()->create([
            'doctor_id' => $this->doctorProfile->id,
            'patient_id' => $this->patientProfile->id,
            'status' => 'confirmed',
            'appointment_date' => now()->format('Y-m-d'),
            'payment_status' => 'paid',
        ]);

        $response = $this->actingAs($this->doctorUser)->get(route('doctor.bookings.show', $todayPaidBooking));
        $response->assertSee('Complete Booking');

        // 2. Today's UNPAID booking -> Should NOT see button
        $todayUnpaidBooking = Booking::factory()->create([
            'doctor_id' => $this->doctorProfile->id,
            'patient_id' => $this->patientProfile->id,
            'status' => 'confirmed',
            'appointment_date' => now()->format('Y-m-d'),
            'payment_status' => 'unpaid',
        ]);

        $response = $this->actingAs($this->doctorUser)->get(route('doctor.bookings.show', $todayUnpaidBooking));
        $response->assertDontSee('Complete Booking');


        // 3. Future booking -> Should NOT see button
        $futureBooking = Booking::factory()->create([
            'doctor_id' => $this->doctorProfile->id,
            'patient_id' => $this->patientProfile->id,
            'status' => 'confirmed',
            'appointment_date' => now()->addDay()->format('Y-m-d'),
            'payment_status' => 'paid',
        ]);

        $response = $this->actingAs($this->doctorUser)->get(route('doctor.bookings.show', $futureBooking));
        $response->assertDontSee('Complete Booking');
    }
}
