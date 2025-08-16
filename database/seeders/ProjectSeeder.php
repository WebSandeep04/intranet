<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Tenant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenants = Tenant::all();

        foreach ($tenants as $tenant) {
            $projects = [
                [
                    'name' => 'Web Development',
                    'description' => 'Complete web application development including frontend, backend, and database design.',
                ],
                [
                    'name' => 'Mobile App Development',
                    'description' => 'Native and cross-platform mobile application development for iOS and Android.',
                ],
                [
                    'name' => 'E-commerce Solution',
                    'description' => 'Complete e-commerce platform with payment gateway integration and inventory management.',
                ],
                [
                    'name' => 'Digital Marketing',
                    'description' => 'Comprehensive digital marketing services including SEO, SEM, and social media marketing.',
                ],
                [
                    'name' => 'UI/UX Design',
                    'description' => 'User interface and user experience design for web and mobile applications.',
                ],
                [
                    'name' => 'Cloud Infrastructure',
                    'description' => 'Cloud setup, migration, and infrastructure management services.',
                ],
            ];

            foreach ($projects as $projectData) {
                Project::create([
                    'name' => $projectData['name'],
                    'description' => $projectData['description'],
                    'tenant_id' => $tenant->id,
                ]);
            }
        }
    }
}
