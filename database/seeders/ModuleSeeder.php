<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\Project;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $projects = Project::all();

        foreach ($projects as $project) {
            $modules = [];

            switch ($project->name) {
                case 'Web Development':
                    $modules = [
                        ['name' => 'Frontend Development', 'description' => 'HTML, CSS, JavaScript, React, Vue.js development'],
                        ['name' => 'Backend Development', 'description' => 'Server-side development with PHP, Node.js, Python'],
                        ['name' => 'Database Design', 'description' => 'Database architecture and optimization'],
                        ['name' => 'API Development', 'description' => 'RESTful API and GraphQL development'],
                        ['name' => 'Testing & QA', 'description' => 'Unit testing, integration testing, and quality assurance'],
                        ['name' => 'Deployment', 'description' => 'Server setup, deployment, and maintenance'],
                    ];
                    break;

                case 'Mobile App Development':
                    $modules = [
                        ['name' => 'iOS Development', 'description' => 'Native iOS app development with Swift/Objective-C'],
                        ['name' => 'Android Development', 'description' => 'Native Android app development with Kotlin/Java'],
                        ['name' => 'Cross-platform Development', 'description' => 'React Native, Flutter, or Xamarin development'],
                        ['name' => 'UI/UX Design', 'description' => 'Mobile app interface and user experience design'],
                        ['name' => 'App Store Optimization', 'description' => 'ASO and app store submission'],
                        ['name' => 'Testing & Debugging', 'description' => 'Mobile app testing and bug fixing'],
                    ];
                    break;

                case 'E-commerce Solution':
                    $modules = [
                        ['name' => 'Platform Setup', 'description' => 'E-commerce platform installation and configuration'],
                        ['name' => 'Payment Gateway Integration', 'description' => 'Payment processor integration'],
                        ['name' => 'Inventory Management', 'description' => 'Product catalog and inventory system'],
                        ['name' => 'Order Management', 'description' => 'Order processing and fulfillment system'],
                        ['name' => 'Customer Management', 'description' => 'Customer accounts and loyalty programs'],
                        ['name' => 'Analytics & Reporting', 'description' => 'Sales analytics and business intelligence'],
                    ];
                    break;

                case 'Digital Marketing':
                    $modules = [
                        ['name' => 'SEO (Search Engine Optimization)', 'description' => 'On-page and off-page SEO optimization'],
                        ['name' => 'SEM (Search Engine Marketing)', 'description' => 'Google Ads and PPC campaigns'],
                        ['name' => 'Social Media Marketing', 'description' => 'Social media strategy and content management'],
                        ['name' => 'Content Marketing', 'description' => 'Blog writing, email marketing, and content strategy'],
                        ['name' => 'Analytics & Tracking', 'description' => 'Google Analytics and conversion tracking'],
                        ['name' => 'AMC (Annual Maintenance Contract)', 'description' => 'Ongoing maintenance and optimization'],
                    ];
                    break;

                case 'UI/UX Design':
                    $modules = [
                        ['name' => 'User Research', 'description' => 'User interviews, surveys, and persona development'],
                        ['name' => 'Wireframing', 'description' => 'Low-fidelity wireframes and user flows'],
                        ['name' => 'Prototyping', 'description' => 'Interactive prototypes and user testing'],
                        ['name' => 'Visual Design', 'description' => 'High-fidelity mockups and design systems'],
                        ['name' => 'Usability Testing', 'description' => 'User testing and feedback collection'],
                        ['name' => 'Design Handoff', 'description' => 'Design specifications and developer handoff'],
                    ];
                    break;

                case 'Cloud Infrastructure':
                    $modules = [
                        ['name' => 'Cloud Migration', 'description' => 'Legacy system migration to cloud platforms'],
                        ['name' => 'Server Setup', 'description' => 'Cloud server configuration and optimization'],
                        ['name' => 'Security Implementation', 'description' => 'Cloud security and compliance setup'],
                        ['name' => 'Monitoring & Logging', 'description' => 'System monitoring and log management'],
                        ['name' => 'Backup & Recovery', 'description' => 'Data backup and disaster recovery planning'],
                        ['name' => 'Performance Optimization', 'description' => 'Cloud performance tuning and scaling'],
                    ];
                    break;

                default:
                    $modules = [
                        ['name' => 'Planning & Analysis', 'description' => 'Project planning and requirements analysis'],
                        ['name' => 'Development', 'description' => 'Core development work'],
                        ['name' => 'Testing', 'description' => 'Quality assurance and testing'],
                        ['name' => 'Deployment', 'description' => 'Production deployment and go-live'],
                        ['name' => 'Maintenance', 'description' => 'Ongoing maintenance and support'],
                    ];
                    break;
            }

            foreach ($modules as $moduleData) {
                Module::create([
                    'name' => $moduleData['name'],
                    'description' => $moduleData['description'],
                    'project_id' => $project->id,
                    'tenant_id' => $project->tenant_id,
                ]);
            }
        }
    }
}
