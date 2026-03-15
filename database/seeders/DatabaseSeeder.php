<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\Models\User;
use App\Models\Patients;
use App\Models\Doctors;
use App\Models\Appointments;
use App\Models\Payments;
use App\Models\MedicalRecords;
use App\Models\DoctorSchedule;
use App\Models\Services;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        User::factory()->create([
            'name' => 'Test Doctor',
            'email' => 'doctor@example.com',
            'password' => Hash::make('password'),
            'role' => 'doctor',
        ]);

        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'doctor',
        ]);


        // --------------------------------------------------
        // 2. Create Patients + Users
        // --------------------------------------------------
        $patients = [];

        for ($i = 1; $i <= 5; $i++) {

            $user = User::create([
                'name' => "Patient $i",
                'email' => "patient$i@example.com",
                'password' => Hash::make('password'),
            ]);

            $patients[] = Patients::create([
                'user_id' => $user->id,
                'first_name' => fake()->firstName(),
                'middle_name' => fake()->optional()->firstName(),
                'last_name' => fake()->lastName(),
                'birthdate' => fake()->dateTimeBetween('-60 years', '-18 years')->format('Y-m-d'),
                'gender' => fake()->randomElement(['Male', 'Female']),
                'contact_number' => '09' . fake()->numberBetween(100000000, 999999999),
                'email' => fake()->unique()->safeEmail(),
                'address' => fake()->address(),
                'emergency_contact_name' => fake()->name(),
                'emergency_contact_number' => '09' . fake()->numberBetween(100000000, 999999999),
            ]);
        }

        // --------------------------------------------------
        // 3. Create Doctors
        // --------------------------------------------------
        $specializations = ['Cardiology', 'Pediatrics', 'Dermatology'];

        foreach ($specializations as $spec) {
            Doctors::create([
                'specialization' => $spec,
                'bio' => "Experienced $spec specialist.",
                'profile_photo' => null,
            ]);
        }

        // --------------------------------------------------
        // 4. Doctor Weekly Schedule (Mon–Fri)
        // --------------------------------------------------
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

        foreach ($days as $day) {
            DoctorSchedule::create([
                'day_of_week' => $day,
                'start_time' => '08:00:00',
                'end_time' => '20:00:00',
                'slot_limit' => 5,
                'is_active' => true,
            ]);
        }

        // --------------------------------------------------
        // 5. Create Appointments + Payments + Medical Records
        // --------------------------------------------------
        foreach ($patients as $patient) {

            $appointment = Appointments::create([
                'patient_id' => $patient->id,
                'appointment_date' => Carbon::now()->addDays(rand(1, 10)),
                'appointment_time' => '10:00:00',
                'status' => fake()->randomElement([
                    'pending',
                    'confirmed',
                    'done',
                ]),
                'payment_status' => 'paid',
                'notes' => 'Regular checkup appointment.',
            ]);

            Payments::create([
                'appointment_id' => $appointment->id,
                'amount' => 500.00,
                'payment_method' => 'gcash',
                'gcash_reference_number' => strtoupper(Str::random(10)),
                'gcash_number' => '09' . fake()->numberBetween(100000000, 999999999),
                'receipt_image' => null,
                'payment_status' => 'approved',
                'reviewed_by' => $admin->id,
                'reviewed_at' => now(),
            ]);

            MedicalRecords::create([
                'patient_id' => $patient->id,
                'doctor_name' => 'Dr. Smith',
                'chief_complaint' => 'Headache and mild fever.',
                'findings' => 'Normal vitals. Mild dehydration.',
                'diagnosis' => 'Viral infection.',
                'treatment_plan' => 'Rest and hydration.',
                'prescription' => 'Paracetamol 500mg',
                'attachments' => json_encode([]),
                'follow_up_date' => Carbon::now()->addWeeks(2),
            ]);
        }

        DB::table('services')->insert([
            [
                'name' => 'Tooth Extraction',
                'description' => 'Removal of damaged or decayed tooth',
                'price' => 2500,
                'discount_price' => 2200,
                'duration' => 60,
                'archived' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Dental Filling',
                'description' => 'Restoration of tooth damaged by decay',
                'price' => 2000,
                'discount_price' => 1800,
                'duration' => 45,
                'archived' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Lazer (Peeling)',
                'description' => 'Laser dental peeling treatment',
                'price' => 3000,
                'discount_price' => 2700,
                'duration' => 60,
                'archived' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Denture (US Plastic)',
                'description' => 'Plastic denture replacement',
                'price' => 8000,
                'discount_price' => 7500,
                'duration' => 120,
                'archived' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Flexible Denture',
                'description' => 'Flexible removable denture',
                'price' => 10000,
                'discount_price' => 9500,
                'duration' => 120,
                'archived' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Braces',
                'description' => 'Orthodontic braces treatment',
                'price' => 50000,
                'discount_price' => 45000,
                'duration' => 90,
                'archived' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Dental Cleaning',
                'description' => 'Removal of plaque and tartar',
                'price' => 1500,
                'discount_price' => 1200,
                'duration' => 30,
                'archived' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
