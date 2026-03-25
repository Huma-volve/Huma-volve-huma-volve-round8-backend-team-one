<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Simulate Doctor Login (using doctor1 as in seeder)
$doctor = User::where('email', 'doctor1@example.com')->first();
if (!$doctor || !$doctor->doctorProfile) {
    die("Doctor 1 not found or has no profile.\n");
}
Auth::login($doctor);

echo "Doctor: " . $doctor->name . "\n";
echo "Profile ID: " . $doctor->doctorProfile->id . "\n";

$slots = $doctor->doctorProfile->getUpcomingSlots();

echo "Total Slots Found: " . $slots->count() . "\n";

if ($slots->isEmpty()) {
    echo "No slots returned from getUpcomingSlots().\n";
    // Check schedules
    $schedules = $doctor->doctorProfile->doctorSchedules;
    echo "Schedules count: " . $schedules->count() . "\n";
    foreach($schedules as $sched) {
        echo "Day: " . $sched->day_of_week . " (" . $sched->start_time . " - " . $sched->end_time . ")\n";
    }
} else {
    echo "First Slot Date: " . $slots->first()['date'] . "\n";
    echo "Last Slot Date: " . $slots->last()['date'] . "\n";
}

$groupedSlots = $slots->filter(function ($slot) {
    return $slot['date'] !== now()->toDateString();
})->groupBy('date');

echo "Grouped Slots Count (Days): " . $groupedSlots->count() . "\n";
echo "JSON Output:\n";
echo json_encode($groupedSlots, JSON_PRETTY_PRINT);
