<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EntryType;
use App\Models\Tenant;

class LeaveTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first tenant (assuming there's at least one)
        $tenant = Tenant::first();
        
        if (!$tenant) {
            $this->command->error('No tenant found. Please run TenantSeeder first.');
            return;
        }

        $leaveTypes = [
            [
                'name' => 'Sick Leave',
                'working_hours' => 0,
                'description' => 'Medical leave for health-related issues',
                'tenant_id' => $tenant->id
            ],
            [
                'name' => 'Casual Leave',
                'working_hours' => 0,
                'description' => 'Personal leave for personal matters',
                'tenant_id' => $tenant->id
            ],
            [
                'name' => 'Annual Leave',
                'working_hours' => 0,
                'description' => 'Paid vacation leave',
                'tenant_id' => $tenant->id
            ],
            [
                'name' => 'Maternity Leave',
                'working_hours' => 0,
                'description' => 'Leave for expecting mothers',
                'tenant_id' => $tenant->id
            ],
            [
                'name' => 'Paternity Leave',
                'working_hours' => 0,
                'description' => 'Leave for new fathers',
                'tenant_id' => $tenant->id
            ],
            [
                'name' => 'Bereavement Leave',
                'working_hours' => 0,
                'description' => 'Leave for family bereavement',
                'tenant_id' => $tenant->id
            ],
            [
                'name' => 'Study Leave',
                'working_hours' => 0,
                'description' => 'Leave for educational purposes',
                'tenant_id' => $tenant->id
            ],
            [
                'name' => 'Unpaid Leave',
                'working_hours' => 0,
                'description' => 'Leave without pay',
                'tenant_id' => $tenant->id
            ]
        ];

        foreach ($leaveTypes as $leaveType) {
            EntryType::updateOrCreate(
                ['name' => $leaveType['name'], 'tenant_id' => $tenant->id],
                $leaveType
            );
        }

        $this->command->info('Leave types seeded successfully!');
    }
}
