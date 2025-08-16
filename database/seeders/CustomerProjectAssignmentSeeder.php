<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Project;
use App\Models\CustomerProject;
use App\Models\CustomerProjectModule;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CustomerProjectAssignmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenants = \App\Models\Tenant::all();

        foreach ($tenants as $tenant) {
            $customers = Customer::where('tenant_id', $tenant->id)->get();
            $projects = Project::where('tenant_id', $tenant->id)->get();

            // Assign projects to customers
            foreach ($customers as $index => $customer) {
                $project = $projects[$index % count($projects)];
                
                $customerProject = CustomerProject::create([
                    'customer_id' => $customer->id,
                    'project_id' => $project->id,
                    'start_date' => now()->subDays(30),
                    'end_date' => now()->addDays(60),
                    'status' => 'in_progress',
                    'description' => 'Sample project assignment for worklog testing',
                    'tenant_id' => $tenant->id,
                ]);

                // Assign some modules to this customer project
                $modules = $project->modules()->take(3)->get();
                foreach ($modules as $module) {
                    CustomerProjectModule::create([
                        'customer_project_id' => $customerProject->id,
                        'module_id' => $module->id,
                        'status' => 'in_progress',
                        'start_date' => now()->subDays(20),
                        'end_date' => now()->addDays(40),
                        'description' => 'Module assignment for worklog testing',
                        'tenant_id' => $tenant->id,
                    ]);
                }
            }
        }
    }
}
