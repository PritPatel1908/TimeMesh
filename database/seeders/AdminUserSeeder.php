<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'user_code' => 'admin',
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'contact_no' => '1234567890',
            'guardian_name' => 'Admin Guardian',
            'guardian_relation' => 'Self',
            'guardian_contact_no' => '1234567890',
            'emergency_contact' => '1234567890',
            'email' => 'admin@example.com',
            'occupation' => 'Administrator',
            'occupation_address' => 'Admin Office',
            'medical_detail' => 'None',
            'joining_date' => Carbon::now(),
            'password' => Hash::make('admin'),
        ]);
    }
}
