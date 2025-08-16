<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserManagerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get users with role_id = 3 (user role)
        $users = User::where('role_id', 3)->get();
        
        if ($users->count() >= 2) {
            // Make the first user a manager for the second user
            $manager = $users->first();
            $subordinate = $users->skip(1)->first();
            
            $subordinate->update(['is_manager' => $manager->id]);
            
            $this->command->info("Manager relationship created: {$manager->name} -> {$subordinate->name}");
            
            // If there's a third user, make the second user their manager
            if ($users->count() >= 3) {
                $thirdUser = $users->skip(2)->first();
                $thirdUser->update(['is_manager' => $subordinate->id]);
                
                $this->command->info("Manager relationship created: {$subordinate->name} -> {$thirdUser->name}");
            }
        }
        
        $this->command->info('User manager relationships seeding completed.');
    }
}
