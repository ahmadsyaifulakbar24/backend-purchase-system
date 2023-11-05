<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'id' => 'a515adb8-5866-407d-8dda-795a6665f17b',
                'location_code' => 'L1',
                'location' => 'HO JAKARTA',
            ],
            [
                'id' => 'd122cce7-667b-4c14-95bc-94c03acc99fb',
                'location_code' => 'L2',
                'location' => 'SHIP 114',
            ],
            [
                'id' => '116be5ed-de85-4b39-b32f-6b6a72291e3d',
                'location_code' => 'L3',
                'location' => 'Federal II',
            ],
            [
                'id' => '68179969-ec2e-40e2-a186-ec2f99f6f193',
                'location_code' => 'L4',
                'location' => 'SHIP 112',
            ],
            [
                'id' => 'b4533df3-d041-4dd6-a81c-774f3ae5d870',
                'location_code' => 'L5',
                'location' => 'SHIP 115',
            ],
            [
                'id' => '5126a0e9-5789-47e0-99d6-5faf1816b6e4',
                'location_code' => 'L6',
                'location' => 'GAS Camelot',
            ],
            [
                'id' => '277ad170-a628-44c2-b0d1-8683d6313d09',
                'location_code' => 'L7',
                'location' => 'SHIP 111',
            ],
            [
                'id' => 'e2061a1d-e7c7-4f8d-a589-c98e5693ac66',
                'location_code' => 'L8',
                'location' => 'INA Permata 1',
            ],
            [
                'id' => 'd88df63f-ba7b-46f3-b2d8-25722c51a9f0',
                'location_code' => 'L9',
                'location' => 'INA Permata 2',
            ],
            [
                'id' => '46729f90-57d9-40fe-884a-dc1f71e6adf9',
                'location_code' => 'L10',
                'location' => 'KING Fisher',
            ],
            [
                'id' => '928a379d-b0cc-4e26-a360-97a0f0d69577',
                'location_code' => 'L11',
                'location' => 'Star Onix',
            ],
            [
                'id' => 'fbd40927-7931-4072-af97-11717b648613',
                'location_code' => 'L12',
                'location' => 'JSJB Jindi',
            ],
            [
                'id' => '32c58a34-5ce5-47fe-931c-0761aa7a920e',
                'location_code' => 'L13',
                'location' => 'CS GHJ',
            ],
        ];

        foreach ($data as $data) {
            Location::create($data);
        }
    }
}
