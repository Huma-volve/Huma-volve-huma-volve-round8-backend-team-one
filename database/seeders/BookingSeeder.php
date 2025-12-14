<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Transaction;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        $bookingsData = [
            [
                'doctor_id' => 1,
                'patient_id' => 1,
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
                'doctor_id' => 2,
                'patient_id' => 2,
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
                'cancelled_by' => 2,
            ],
            [
                'doctor_id' => 3,
                'patient_id' => 3,
                'appointment_date' => '2025-12-10',
                'appointment_time' => '11:00:00',
                'status' => 'confirmed',
                'price_at_booking' => 200,
                'payment_method' => 'cash',
                'payment_status' => 'unpaid',
                'notes' => 'First consultation',
            ],
            [
                'doctor_id' => 1,
                'patient_id' => 4,
                'appointment_date' => '2025-12-11',
                'appointment_time' => '12:00:00',
                'status' => 'pending',
                'price_at_booking' => 150,
                'payment_method' => 'stripe',
                'payment_status' => 'unpaid',
                'notes' => 'Routine check-up',
            ],
            [
                'doctor_id' => 2,
                'patient_id' => 5,
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
                'doctor_id' => 3,
                'patient_id' => 6,
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
                'doctor_id' => 1,
                'patient_id' => 2,
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
                'cancelled_by' => 2,
            ],
            [
                'doctor_id' => 2,
                'patient_id' => 3,
                'appointment_date' => '2025-12-15',
                'appointment_time' => '09:30:00',
                'status' => 'confirmed',
                'price_at_booking' => 120,
                'payment_method' => 'stripe',
                'payment_status' => 'paid',
                'payment_transaction_id' => 'TXN10004',
                'notes' => 'Consultation',
            ],
            [
                'doctor_id' => 3,
                'patient_id' => 1,
                'appointment_date' => '2025-12-16',
                'appointment_time' => '10:30:00',
                'status' => 'pending',
                'price_at_booking' => 200,
                'payment_method' => 'paypal',
                'payment_status' => 'unpaid',
                'notes' => 'Routine check-up',
            ],
            [
                'doctor_id' => 1,
                'patient_id' => 5,
                'appointment_date' => '2025-12-17',
                'appointment_time' => '11:30:00',
                'status' => 'completed',
                'price_at_booking' => 150,
                'payment_method' => 'stripe',
                'payment_status' => 'paid',
                'payment_transaction_id' => 'TXN10005',
                'notes' => 'Follow-up visit',
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
