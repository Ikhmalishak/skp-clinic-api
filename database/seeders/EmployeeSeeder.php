<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use Faker\Factory as Faker;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $faker = Faker::create();

        for ($i = 1; $i <= 200; $i++) {
            Employee::create([
                'employee_id' => 'EMP' . str_pad($i, 3, '0', STR_PAD_LEFT), // EMP001, EMP002...
                'name' => $faker->name,
                'race' => $faker->randomElement(['Asian', 'Caucasian', 'Hispanic', 'African', 'Mixed']),
                'nric_number' => $faker->numerify('##########'), // Random 10-digit number
                'passport_number' => $faker->bothify('??#######'), // Random passport format
                'nationatility' => $faker->country,
                'base' => $faker->randomElement(['HQ', 'Branch A', 'Branch B']),
                'department' => $faker->randomElement(['IT', 'HR', 'Finance', 'Marketing']),
                'company' => $faker->company,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}

