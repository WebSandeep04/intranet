<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // $this->call(StatesTableSeeder::class);
        $this->call(RolesTableSeeder::class);
        $this->call(TenantSeeder::class); // Must run first to create default tenant
        $this->call(StatesAndCitiesSeeder::class);
        $this->call(SalesStatusSeeder::class);
        $this->call(UsersSeeder::class);
        $this->call(UserRoleSeeder::class); // Add new user role seeder
        $this->call(UserManagerSeeder::class); // Add manager relationships
        $this->call(ProjectSeeder::class);
        $this->call(ModuleSeeder::class);
        $this->call(CustomerSeeder::class);
        $this->call(EntryTypeSeeder::class); // Add entry types for worklog
        $this->call(CustomerProjectAssignmentSeeder::class); // Add customer project assignments
        $this->call(WorklogUserSeeder::class); // Enable worklog for specific users
        $this->call(HolidaySeeder::class); // Add sample holidays
        $this->call(ManagerSeeder::class); // Add manager relationships for existing users
        $this->call(AttendanceSeeder::class); // Add sample attendance data
       
    }
}
