<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class QueueSeeder extends Seeder
{
    public function run()
    {
        // Sample employee IDs (Ensure these exist in employees table)
        $employeeIds = DB::table('employees')->pluck('employee_id')->toArray();
        
        if (empty($employeeIds)) {
            $this->command->warn("⚠️ No employees found! Add employees first before seeding queues.");
            return;
        }

        // Insert sample queue data
        $queues = [];

        for ($i = 1; $i <= 20; $i++) {
            $queues[] = [
                'employee_id'     => $employeeIds[array_rand($employeeIds)], // Random employee
                'queue_number'    => $i,
                'queue_date'      => Carbon::today()->toDateString(), // Today's date
                'status'          => $i <= 5 ? 'completed' : ($i <= 10 ? 'in_consultation' : 'waiting'),
                'time_registered' => Carbon::now()->subMinutes(rand(5, 120)), // Random past time
                'time_called'     => $i <= 10 ? Carbon::now()->subMinutes(rand(1, 30)) : null,
                'created_at'      => now(),
                'updated_at'      => now(),
            ];
        }

        DB::table('queues')->insert($queues);

        $this->command->info("✅ Successfully seeded 20 queue records!");
    }
}
