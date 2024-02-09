<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create(['name' => 'super-admin', 'guard_name' => 'api']);
        Role::create(['name' => 'bod', 'guard_name' => 'api']);
        Role::create(['name' => 'operation', 'guard_name' => 'api']);
        Role::create(['name' => 'purchasing', 'guard_name' => 'api']);
        Role::create(['name' => 'camp-boss', 'guard_name' => 'api']);
        Role::create(['name' => 'finance', 'guard_name' => 'api']);
        Role::create(['name' => 'store-keeper', 'guard_name' => 'api']);
    }
}
