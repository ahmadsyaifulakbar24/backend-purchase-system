<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::create([
            'id' => '5594262c-5a60-4768-b958-58892e39032e',
            'code' => '1',
            'name' => 'Super Admin',
            'username' => 'superadmin',
            'email' => 'superadmin@admin.com',
            'password' => Hash::make('12345678'),
            'status' => 'active',
            'department_id' => 'acd6f5f1-761c-4b9b-b565-25c061089ee3',
            'location_id' => 'a515adb8-5866-407d-8dda-795a6665f17b',
            'address' => 'Jakarta',
        ]);
        $user->assignRole('super-admin');

    }
}
