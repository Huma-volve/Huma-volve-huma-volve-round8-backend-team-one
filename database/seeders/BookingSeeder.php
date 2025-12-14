<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Transaction;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        $doctors = \App\Models\DoctorProfile::pluck('id');
        $patients = \App\Models\PatientProfile::pluck('id');
        $userIds = \App\Models\User::pluck('id');

        if ($doctors->isEmpty() || $patients->isEmpty() || $userIds->isEmpty()) {
            return;
        }

        $bookingsData = [
            [
                'doctor_id' => $doctors->random(),
                'patient_id' => $patients->random(),
                'appointment_date' => '2025-12-08',
                'appointment_time' => '09:00:00',
                'status' => 'completed',
                'price_at_booking' => 150,
                'payment_method' => 'stripe',
                'payment_status' => 'paid',
                'payment_transaction_id' => 'TXN10001',
                'notes' => 'Follow-up visit',
            ],
            // ... (rest of data, but we should probably automate this or just fix the hardcoded ones if specific logic needed)
        ];

        // Since the original seeder had specific scenarios, let's map them but use dynamic IDs.
        // For simplicity and robustness, I will just generate random valid bookings or try to map 1 -> first, 2 -> second.

        $doc1 = $doctors[0] ?? null;
        $doc2 = $doctors[1] ?? null;
        $doc3 = $doctors[2] ?? null;

        $pat1 = $patients[0] ?? null;
        $pat2 = $patients[1] ?? null;
        $pat3 = $patients[2] ?? null;
        $pat4 = $patients[3] ?? null;
        $pat5 = $patients[4] ?? null;
        $pat6 = $patients[5] ?? null;

        if (! $doc1 || ! $pat1) {
            return;
        }

        $bookingsData = [
            [
                'doctor_id' => $doc1,
                'patient_id' => $pat1,
                'appointment_date' => '2025-12-08',
                'appointment_time' => '09:00:00',
                'status' => 'completed',
                'price_at_booking' => 150,
                'payment_method' => 'stripe',
                'payment_status' => 'paid',
                'payment_transaction_id' => 'TXN10001',
                'notes' => 'Follow-up visit',
            ],
            [
                'doctor_id' => $doc2 ?? $doc1,
                'patient_id' => $pat2 ?? $pat1,
                'appointment_date' => '2025-12-09',
                'appointment_time' => '10:00:00',
                'status' => 'cancelled',
                'price_at_booking' => 120,
                'payment_method' => 'paypal',
                'payment_status' => 'refunded',
                'payment_transaction_id' => null,
                'notes' => 'Patient cancelled',
                'cancellation_reason' => 'Feeling better',
                'cancelled_at' => '2025-12-08 11:00:00',
                'cancelled_by' => $userIds->random(),
            ],
            [
                'doctor_id' => $doc3 ?? $doc1,
                'patient_id' => $pat3 ?? $pat1,
                'appointment_date' => '2025-12-10',
                'appointment_time' => '11:00:00',
                'status' => 'confirmed',
                'price_at_booking' => 200,
                'payment_method' => 'cash',
                'payment_status' => 'unpaid',
                'notes' => 'First consultation',
            ],
            [
                'doctor_id' => $doc1,
                'patient_id' => $pat4 ?? $pat1,
                'appointment_date' => '2025-12-11',
                'appointment_time' => '12:00:00',
                'status' => 'pending',
                'price_at_booking' => 150,
                'payment_method' => 'stripe',
                'payment_status' => 'unpaid',
                'notes' => 'Routine check-up',
            ],
            [
                'doctor_id' => $doc2 ?? $doc1,
                'patient_id' => $pat5 ?? $pat1,
                'appointment_date' => '2025-12-12',
                'appointment_time' => '13:00:00',
                'status' => 'completed',
                'price_at_booking' => 120,
                'payment_method' => 'paypal',
                'payment_status' => 'paid',
                'payment_transaction_id' => 'TXN10002',
                'notes' => 'Skin consultation',
            ],
            [
                'doctor_id' => $doc3 ?? $doc1,
                'patient_id' => $pat6 ?? $pat1,
                'appointment_date' => '2025-12-13',
                'appointment_time' => '14:00:00',
                'status' => 'completed',
                'price_at_booking' => 200,
                'payment_method' => 'stripe',
                'payment_status' => 'paid',
                'payment_transaction_id' => 'TXN10003',
                'notes' => 'Surgery follow-up',
            ],
            [
                'doctor_id' => $doc1,
                'patient_id' => $pat2 ?? $pat1,
                'appointment_date' => '2025-12-14',
                'appointment_time' => '15:00:00',
                'status' => 'cancelled',
                'price_at_booking' => 150,
                'payment_method' => 'cash',
                'payment_status' => 'refunded',
                'payment_transaction_id' => null,
                'notes' => 'Patient cancelled',
                'cancellation_reason' => 'Schedule conflict',
                'cancelled_at' => '2025-12-13 10:00:00',
                'cancelled_by' => $userIds->random(),
            ],
            [
                'doctor_id' => $doc2 ?? $doc1,
                'patient_id' => $pat3 ?? $pat1,
                'appointment_date' => '2025-12-15',
                'appointment_time' => '09:30:00',
                'status' => 'confirmed',
                'price_at_booking' => 120,
                'payment_method' => 'stripe',
                'payment_status' => 'paid',
                'payment_transaction_id' => 'TXN10004',
                'notes' => 'Consultation',
            ],
        ];

        foreach ($bookingsData as $data) {
            $booking = Booking::create($data);

            if (isset($data['payment_transaction_id']) && $data['payment_status'] === 'paid') {
                Transaction::create([
                    'booking_id' => $booking->id,
                    'external_id' => $data['payment_transaction_id'],
                    'amount' => $data['price_at_booking'],
                    'type' => 'payment',
                    'status' => 'success',
                    'gateway' => $data['payment_method'],
                    'currency' => 'EGP',
                    'payload' => json_encode(['transaction_id' => $data['payment_transaction_id']]),
                ]);
            }
        }
    }
}
