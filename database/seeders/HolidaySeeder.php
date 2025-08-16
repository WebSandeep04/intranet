<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Holiday;
use App\Models\Tenant;

class HolidaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenants = Tenant::all();
        
        foreach ($tenants as $tenant) {
            // Add some sample holidays for 2025
            $holidays = [
                ['name' => 'New Year\'s Day', 'date' => '2025-01-01'],
                ['name' => 'Republic Day', 'date' => '2025-01-26'],
                ['name' => 'Independence Day', 'date' => '2025-08-15'],
                ['name' => 'Gandhi Jayanti', 'date' => '2025-10-02'],
                ['name' => 'Christmas Day', 'date' => '2025-12-25'],
            ];
            
            foreach ($holidays as $holiday) {
                Holiday::create([
                    'name' => $holiday['name'],
                    'holiday_date' => $holiday['date'],
                    'tenant_id' => $tenant->id,
                ]);
            }
        }
        
        $this->command->info('Sample holidays created for all tenants.');
    }
}
