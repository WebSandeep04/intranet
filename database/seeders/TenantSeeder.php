<?php

namespace Database\Seeders;

use App\Models\Tenant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default tenant first (ID = 1)
        Tenant::firstOrCreate(
            ['id' => 1],
            [
                'tenant_name' => 'Default Tenant',
                'tenant_code' => 'TEN-DEFAULT'
            ]
        );

        // Create additional sample tenants
        $tenants = [
            ['tenant_name' => 'Demo Company 1'],
            ['tenant_name' => 'Demo Company 2'],
            ['tenant_name' => 'Demo Company 3'],
        ];

        foreach ($tenants as $tenant) {
            Tenant::firstOrCreate(
                ['tenant_name' => $tenant['tenant_name']],
                $tenant
            );
        }
    }
}
