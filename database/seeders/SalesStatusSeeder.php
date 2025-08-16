<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SalesStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = ['Close_lost', 'Close_win', 'Cold', 'Warm', 'Hot'];

        foreach ($statuses as $status) {
            DB::table('sales_status')->insert([
                'status_name' => $status,
                'tenant_id' => 1, // Default tenant
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
