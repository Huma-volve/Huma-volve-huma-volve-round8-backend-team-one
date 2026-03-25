<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_dashboard()
    {
        $admin = User::factory()->create([
            'user_type' => 'admin',
            'email' => 'admin_test@example.com',
        ]);

        $response = $this->actingAs($admin)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.dashboard');
        // Check if stats variables are passed to the view
        $response->assertViewHas(['totalPatients', 'totalDoctors', 'monthlyStats']);
    }

    public function test_admin_dashboard_stats_calculation()
    {
        $admin = User::factory()->create(['user_type' => 'admin']);

        // Create 3 Patients
        User::factory()->count(3)->create(['user_type' => 'patient']);

        // Create 2 Doctors
        User::factory()->count(2)->create(['user_type' => 'doctor']);

        // Create Bookings with status 'completed' (Monthly stats now only shows completed bookings)
        // 2 Completed bookings (Profit: 200)
        Booking::factory()->count(2)->create([
            'status' => 'completed',
            'payment_status' => 'paid',
            'price_at_booking' => 100.00,
            'appointment_date' => now(), // ensure current month
        ]);
        // 1 Pending booking (should NOT appear in monthly stats since not completed)
        Booking::factory()->create([
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'price_at_booking' => 50.00,
            'appointment_date' => now(),
        ]);

        $response = $this->actingAs($admin)->get(route('dashboard'));

        $response->assertStatus(200);

        $viewData = $response->original->getData();

        $this->assertGreaterThanOrEqual(3, $viewData['totalPatients']);
        $this->assertGreaterThanOrEqual(2, $viewData['totalDoctors']);

        $monthlyStats = $viewData['monthlyStats'];
        $this->assertNotEmpty($monthlyStats);

        $stat = $monthlyStats->first();
        $this->assertNotNull($stat->year);
        $this->assertNotNull($stat->month);
        $this->assertNotNull($stat->total_bookings);
        $this->assertNotNull($stat->net_profit);
    }

    public function test_admin_can_view_patients_list()
    {
        $admin = User::factory()->create(['user_type' => 'admin']);
        $patient = User::factory()->create([
            'user_type' => 'patient',
            'name' => 'UniqueNameForTest'
        ]);

        $response = $this->actingAs($admin)->get(route('admin.patients.index'));

        $response->assertStatus(200);
        $response->assertSee('UniqueNameForTest');
    }

    public function test_admin_can_block_patient()
    {
        $admin = User::factory()->create(['user_type' => 'admin']);
        $patient = User::factory()->create([
            'user_type' => 'patient',
            'is_blocked' => false
        ]);

        $response = $this->actingAs($admin)->post(route('admin.patients.toggle-block', $patient));

        $response->assertRedirect();

        $this->assertDatabaseHas('users', [
            'id' => $patient->id,
            'is_blocked' => true,
        ]);

        // Toggle back to unblock
        $response = $this->actingAs($admin)->post(route('admin.patients.toggle-block', $patient));
        $this->assertDatabaseHas('users', [
            'id' => $patient->id,
            'is_blocked' => false,
        ]);
    }

    public function test_admin_can_filter_bookings()
    {
        $admin = User::factory()->create(['user_type' => 'admin']);

        // Create a specific booking
        $booking = Booking::factory()->create([
            'status' => 'cancelled',
            'price_at_booking' => 123.45
        ]);

        // Access index page
        $response = $this->actingAs($admin)->get(route('admin.bookings.index'));
        $response->assertStatus(200);

        // Filter by status 'cancelled'
        $response = $this->actingAs($admin)->get(route('admin.bookings.index', ['status' => 'cancelled']));
        $response->assertStatus(200);
        $response->assertSee(number_format(123.45, 2)); // Check if price is visible
    }
    public function test_admin_can_filter_bookings_by_doctor()
    {
        $admin = User::factory()->create(['user_type' => 'admin']);
        $doctor1 = User::factory()->create(['user_type' => 'doctor']);
        $doctor2 = User::factory()->create(['user_type' => 'doctor']);

        // Create bookings for doctor 1
        Booking::factory()->create([
            'doctor_id' => \App\Models\DoctorProfile::factory()->create(['user_id' => $doctor1->id])->id,
            'price_at_booking' => 100
        ]);

        // Create booking for doctor 2
        Booking::factory()->create([
            'doctor_id' => \App\Models\DoctorProfile::factory()->create(['user_id' => $doctor2->id])->id,
            'price_at_booking' => 200
        ]);

        // Filter by Doctor 1
        $response = $this->actingAs($admin)->get(route('admin.bookings.index', ['doctor_id' => $doctor1->id]));
        $response->assertStatus(200);
        $response->assertSee(number_format(100, 2));
        $response->assertDontSee(number_format(200, 2));
    }
}
