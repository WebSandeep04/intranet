<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class WorklogUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Enable worklog for admin users (role_id = 1)
        User::where('role_id', 1)->update(['is_worklog' => 1]);
        
        // Enable worklog for some sales users (role_id = 2) - first 3 users
        User::where('role_id', 2)->limit(3)->update(['is_worklog' => 1]);
        
        $this->command->info('Worklog permissions updated for users.');
    }
}
