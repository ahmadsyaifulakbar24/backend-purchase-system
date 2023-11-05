<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use JetBrains\PhpStorm\Deprecated;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'id' => 'acd6f5f1-761c-4b9b-b565-25c061089ee3',
                'department_code' => 'D1',
                'department' => 'BOD',
            ],
            [
                'id' => 'd66b5a13-b26c-45b7-897c-d118aed1c6b4',
                'department_code' => 'D2',
                'department' => 'Operation',
            ],
            [
                'id' => 'bbde98d8-2ab4-45f9-b121-72ef55292be1',
                'department_code' => 'D3',
                'department' => 'Purchasing',
            ],
            [
                'id' => '77b03d86-d662-4f97-b05b-73eb900859c0',
                'department_code' => 'D4',
                'department' => 'Finance',
            ],
            [
                'id' => '3efdf2e0-6f7b-4389-8ef7-86e77887f42f',
                'department_code' => 'D5',
                'department' => 'Camp Boss',
            ],
        ];

        foreach($data as $data) {
            Department::create($data);
        }
    }
}
