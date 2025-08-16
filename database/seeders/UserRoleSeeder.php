<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the user role (role_id = 4)
        $userRole = Role::where('role_name', 'user')->first();
        
        if ($userRole) {
            // Create sample users with the new "user" role
            $users = [
                [
                    'name' => 'John Doe',
                    'email' => 'john.doe@example.com',
                    'password' => Hash::make('password'),
                    'role_id' => $userRole->id,
                    'is_worklog' => 1,
                    'is_manager' => null,
                    'tenant_id' => 1,
                ],
                [
                    'name' => 'Jane Smith',
                    'email' => 'jane.smith@example.com',
                    'password' => Hash::make('password'),
                    'role_id' => $userRole->id,
                    'is_worklog' => 1,
                    'is_manager' => null,
                    'tenant_id' => 1,
                ],
                [
                    'name' => 'Mike Johnson',
                    'email' => 'mike.johnson@example.com',
                    'password' => Hash::make('password'),
                    'role_id' => $userRole->id,
                    'is_worklog' => 1,
                    'is_manager' => null,
                    'tenant_id' => 1,
                ]
            ];

            foreach ($users as $userData) {
                // Check if user already exists
                $existingUser = User::where('email', $userData['email'])->first();
                if (!$existingUser) {
                    User::create($userData);
                    $this->command->info("Created user: {$userData['name']}");
                } else {
                    // Update existing user to have the correct role and worklog access
                    $existingUser->update([
                        'role_id' => $userData['role_id'],
                        'is_worklog' => $userData['is_worklog'],
                        'is_manager' => $userData['is_manager']
                    ]);
                    $this->command->info("Updated user: {$userData['name']} to role_id {$userData['role_id']}");
                }
            }
        }

        $this->command->info('User role seeding completed.');
    }
}
