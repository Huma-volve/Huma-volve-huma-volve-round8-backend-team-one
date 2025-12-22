<?php

use App\Models\User;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- VERIFICATION START ---\n";

$missingDoctorProfiles = User::where('user_type', 'doctor')->doesntHave('doctorProfile')->count();
echo "Doctors without profile: " . $missingDoctorProfiles . " (Expected: 0)\n";

$missingPatientProfiles = User::where('user_type', 'patient')->doesntHave('patientProfile')->count();
echo "Patients without profile: " . $missingPatientProfiles . " (Expected: 0)\n";

$doctor1 = User::where('email', 'doctor1@example.com')->first();
if ($doctor1 && $doctor1->doctorProfile && $doctor1->doctorProfile->license_number === 'LIC-10001') {
    echo "Doctor 1 Static Data: CORRECT\n";
} else {
    echo "Doctor 1 Static Data: INCORRECT\n";
}

$doctor5 = User::where('email', 'doctor5@example.com')->first();
if ($doctor5 && $doctor5->doctorProfile) {
    echo "Doctor 5 Profile Exists: YES\n";
} else {
    echo "Doctor 5 Profile Exists: NO\n";
}

echo "--- VERIFICATION END ---\n";
