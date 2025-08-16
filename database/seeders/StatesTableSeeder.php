<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatesTableSeeder extends Seeder
{
    public function run()
    {
        $states = [
            ['id' => 1, 'state_name' => 'Andhra Pradesh'],
            ['id' => 2, 'state_name' => 'Arunachal Pradesh'],
            ['id' => 3, 'state_name' => 'Assam'],
            ['id' => 4, 'state_name' => 'Bihar'],
            ['id' => 5, 'state_name' => 'Chhattisgarh'],
            ['id' => 6, 'state_name' => 'Goa'],
            ['id' => 7, 'state_name' => 'Gujarat'],
            ['id' => 8, 'state_name' => 'Haryana'],
            ['id' => 9, 'state_name' => 'Himachal Pradesh'],
            ['id' => 10, 'state_name' => 'Jharkhand'],
            ['id' => 11, 'state_name' => 'Karnataka'],
            ['id' => 12, 'state_name' => 'Kerala'],
            ['id' => 13, 'state_name' => 'Madhya Pradesh'],
            ['id' => 14, 'state_name' => 'Maharashtra'],
            ['id' => 15, 'state_name' => 'Manipur'],
            ['id' => 16, 'state_name' => 'Meghalaya'],
            ['id' => 17, 'state_name' => 'Mizoram'],
            ['id' => 18, 'state_name' => 'Nagaland'],
            ['id' => 19, 'state_name' => 'Odisha'],
            ['id' => 20, 'state_name' => 'Punjab'],
            ['id' => 21, 'state_name' => 'Rajasthan'],
            ['id' => 22, 'state_name' => 'Sikkim'],
            ['id' => 23, 'state_name' => 'Tamil Nadu'],
            ['id' => 24, 'state_name' => 'Telangana'],
            ['id' => 25, 'state_name' => 'Tripura'],
            ['id' => 26, 'state_name' => 'Uttar Pradesh'],
            ['id' => 27, 'state_name' => 'Uttarakhand'],
            ['id' => 28, 'state_name' => 'West Bengal'],
            ['id' => 29, 'state_name' => 'Andaman and Nicobar Islands'],
            ['id' => 30, 'state_name' => 'Chandigarh'],
            ['id' => 31, 'state_name' => 'Dadra and Nagar Haveli and Daman and Diu'],
            ['id' => 32, 'state_name' => 'Lakshadweep'],
            ['id' => 33, 'state_name' => 'Delhi'],
            ['id' => 34, 'state_name' => 'Puducherry'],
            ['id' => 35, 'state_name' => 'Ladakh'],
            ['id' => 36, 'state_name' => 'Jammu and Kashmir'],
            ['id' => 37, 'state_name' => 'New Delhi'],
        ];

        DB::table('states')->insert($states);
    }
}
