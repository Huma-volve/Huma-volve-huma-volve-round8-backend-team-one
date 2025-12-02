<?php

namespace Database\Seeders;

use App\Models\Speciality;
use Illuminate\Database\Seeder;

class SpecialitySeeder extends Seeder
{
    public function run(): void
    {
        $specialities = [
            ['name' => 'General Practitioner', 'image' => 'gp.png'],
            ['name' => 'Cardiologist', 'image' => 'cardio.png'],
            ['name' => 'Dermatologist', 'image' => 'derma.png'],
            ['name' => 'Pediatrician', 'image' => 'pedia.png'],
            ['name' => 'Neurologist', 'image' => 'neuro.png'],
            ['name' => 'Orthopedist', 'image' => 'ortho.png'],
            ['name' => 'Dentist', 'image' => 'dentist.png'],
            ['name' => 'Ophthalmologist', 'image' => 'eye.png'],
            ['name' => 'Psychiatrist', 'image' => 'psych.png'],
            ['name' => 'Urologist', 'image' => 'uro.png'],
        ];

        foreach ($specialities as $spec) {
            Speciality::create($spec);
        }
    }
}
