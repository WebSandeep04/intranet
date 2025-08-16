<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Attendance;
use App\Models\Movement;
use App\Models\User;
use Carbon\Carbon;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get a user to create sample attendance for
        $user = User::first();
        
        if (!$user) {
            $this->command->info('No users found. Please run UserSeeder first.');
            return;
        }

        // Create sample attendance for the last 7 days
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            
            // Skip weekends (Saturday = 6, Sunday = 0)
            if ($date->dayOfWeek === 0 || $date->dayOfWeek === 6) {
                continue;
            }

            $attendance = Attendance::create([
                'user_id' => $user->id,
                'date' => $date->format('Y-m-d'),
                'tenant_id' => $user->tenant_id,
            ]);

            // Create sample movements for each day
            $this->createSampleMovements($attendance, $date);
        }

        $this->command->info('Sample attendance data created successfully!');
    }

    private function createSampleMovements($attendance, $date)
    {
        // Office punch in at 9 AM
        Movement::create([
            'attendance_id' => $attendance->id,
            'movement_type' => 'office',
            'movement_action' => 'in',
            'time' => $date->copy()->setTime(9, 0, 0),
            'description' => 'Morning punch in',
            'tenant_id' => $attendance->tenant_id
        ]);

        // Break start at 12 PM
        Movement::create([
            'attendance_id' => $attendance->id,
            'movement_type' => 'break',
            'movement_action' => 'start',
            'time' => $date->copy()->setTime(12, 0, 0),
            'description' => 'Lunch break',
            'tenant_id' => $attendance->tenant_id
        ]);

        // Break end at 1 PM
        Movement::create([
            'attendance_id' => $attendance->id,
            'movement_type' => 'break',
            'movement_action' => 'end',
            'time' => $date->copy()->setTime(13, 0, 0),
            'description' => 'Lunch break ended',
            'tenant_id' => $attendance->tenant_id
        ]);

        // Field work start at 2 PM (some days)
        if ($date->dayOfWeek % 2 === 0) {
            Movement::create([
                'attendance_id' => $attendance->id,
                'movement_type' => 'field',
                'movement_action' => 'in',
                'time' => $date->copy()->setTime(14, 0, 0),
                'description' => 'Field work started',
                'tenant_id' => $attendance->tenant_id
            ]);

            Movement::create([
                'attendance_id' => $attendance->id,
                'movement_type' => 'field',
                'movement_action' => 'out',
                'time' => $date->copy()->setTime(16, 0, 0),
                'description' => 'Field work completed',
                'tenant_id' => $attendance->tenant_id
            ]);
        }

        // Office punch out at 6 PM
        Movement::create([
            'attendance_id' => $attendance->id,
            'movement_type' => 'office',
            'movement_action' => 'out',
            'time' => $date->copy()->setTime(18, 0, 0),
            'description' => 'Evening punch out',
            'tenant_id' => $attendance->tenant_id
        ]);
    }
}
