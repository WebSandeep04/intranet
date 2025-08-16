<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StatesAndCitiesSeeder extends Seeder
{
    public function run(): void
    {
        $statesWithCities = [
            'Maharashtra' => ['Mumbai', 'Pune', 'Nagpur'],
            'Karnataka' => ['Bangalore', 'Mysore', 'Mangalore'],
            'Tamil Nadu' => ['Chennai', 'Coimbatore', 'Madurai'],
            'Uttar Pradesh' => ['Lucknow', 'Kanpur', 'Varanasi'],
        ];

        foreach ($statesWithCities as $state => $cities) {
            $stateId = DB::table('states')->insertGetId([
                'state_name' => $state,
                'tenant_id' => 1, // Default tenant
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            foreach ($cities as $city) {
                DB::table('cities')->insert([
                    'state_id' => $stateId,
                    'city_name' => $city,
                    'tenant_id' => 1, // Default tenant
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
        }
    }
}
