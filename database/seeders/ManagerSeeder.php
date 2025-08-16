<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class ManagerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all users with worklog permission
        $users = User::where('is_worklog', 1)->get();
        
        if ($users->count() >= 2) {
            // Set the first user as manager for the second user
            $manager = $users->first();
            $subordinate = $users->skip(1)->first();
            
            $subordinate->update(['is_manager' => $manager->id]);
            
            $this->command->info("Manager relationship created: {$manager->name} -> {$subordinate->name}");
        }
        
        $this->command->info('Manager relationships updated.');
    }
}
