<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Tenant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenants = Tenant::all();

        foreach ($tenants as $tenant) {
            $customers = [
                [
                    'name' => 'Rajesh Kumar',
                    'email' => 'rajesh@example.com',
                    'phone' => '+91-9876543210',
                    'company_name' => 'Tech Solutions Pvt Ltd',
                    'address' => '123 Tech Park, Bangalore, Karnataka',
                ],
                [
                    'name' => 'Priya Sharma',
                    'email' => 'priya@example.com',
                    'phone' => '+91-8765432109',
                    'company_name' => 'Digital Innovations',
                    'address' => '456 Innovation Street, Mumbai, Maharashtra',
                ],
                [
                    'name' => 'Amit Patel',
                    'email' => 'amit@example.com',
                    'phone' => '+91-7654321098',
                    'company_name' => 'E-commerce Express',
                    'address' => '789 Business Center, Delhi, NCR',
                ],
                [
                    'name' => 'Neha Singh',
                    'email' => 'neha@example.com',
                    'phone' => '+91-6543210987',
                    'company_name' => 'Marketing Masters',
                    'address' => '321 Marketing Avenue, Pune, Maharashtra',
                ],
                [
                    'name' => 'Vikram Malhotra',
                    'email' => 'vikram@example.com',
                    'phone' => '+91-5432109876',
                    'company_name' => 'Cloud Solutions Inc',
                    'address' => '654 Cloud Street, Hyderabad, Telangana',
                ],
            ];

            foreach ($customers as $customerData) {
                Customer::create([
                    'name' => $customerData['name'],
                    'email' => $customerData['email'],
                    'phone' => $customerData['phone'],
                    'company_name' => $customerData['company_name'],
                    'address' => $customerData['address'],
                    'tenant_id' => $tenant->id,
                ]);
            }
        }
    }
}
