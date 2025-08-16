<?php

namespace Database\Seeders;

use App\Models\EntryType;
use App\Models\Tenant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EntryTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenants = Tenant::all();

        foreach ($tenants as $tenant) {
            $entryTypes = [
                [
                    'name' => 'Full Day',
                    'working_hours' => 8,
                    'description' => 'Complete working day - 8 hours',
                ],
                [
                    'name' => 'Half Day',
                    'working_hours' => 4,
                    'description' => 'Half working day - 4 hours',
                ],
                [
                    'name' => 'Leave',
                    'working_hours' => 0,
                    'description' => 'No working hours - Leave day',
                ],
            ];

            foreach ($entryTypes as $entryTypeData) {
                EntryType::create([
                    'name' => $entryTypeData['name'],
                    'working_hours' => $entryTypeData['working_hours'],
                    'description' => $entryTypeData['description'],
                    'tenant_id' => $tenant->id,
                ]);
            }
        }
    }
}
