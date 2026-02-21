<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HospitalAdmin;
use Illuminate\Support\Facades\Hash;

class HospitalAdminSeeder extends Seeder
{
    public function run(): void
    {
        HospitalAdmin::create([
            'hospital_name' => 'City Hospital',
            'email'         => 'admin@cityhospital.com',
            'password'      => Hash::make('password123'), // always hash passwords!
            'contact'       => '09123456789',
            'address'       => '123 Main Street, Cityville',
        ]);
    }
}
