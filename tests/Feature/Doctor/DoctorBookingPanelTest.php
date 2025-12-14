<?php

namespace Tests\Feature\Doctor;

use App\Models\Booking;
use App\Models\DoctorProfile;
use App\Models\PatientProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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
        $response->assertViewHas('bookings', function($bookings) {
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
}
