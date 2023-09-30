<?php

namespace Database\Seeders;

use App\Models\CostCenter;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CostCenterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'id' => 'e372713e-ac3f-46ef-a085-44fca6f32302',
                'cost_center_code' => '1',
                'cost_center' => 'HO JAKARTA',
            ],
            [
                'id' => '96015f73-fabd-40a4-8452-0138c39bf8a4',
                'cost_center_code' => '2',
                'cost_center' => 'SHIP 114',
            ],
            [
                'id' => 'da49f5e0-7608-4501-8ac9-3169a69874ab',
                'cost_center_code' => '3',
                'cost_center' => 'Federal II',
            ],
            [
                'id' => '34a8bbc3-e539-4487-bcd9-15bceafb72c2',
                'cost_center_code' => '4',
                'cost_center' => 'SHIP 112',
            ],
            [
                'id' => '16854d1c-690c-4d36-a3e6-42b39cd21c7f',
                'cost_center_code' => '5',
                'cost_center' => 'SHIP 115',
            ],
            [
                'id' => '08a46c6f-9f58-4d00-bf11-405407209ba4',
                'cost_center_code' => '6',
                'cost_center' => 'GAS Camelot',
            ],
            [
                'id' => '3c5daa2b-ceb1-4b66-8580-92dabea2d46b',
                'cost_center_code' => '7',
                'cost_center' => 'SHIP 111',
            ],
            [
                'id' => 'a7f7d534-46a4-4860-8985-fdecd8f35644',
                'cost_center_code' => '8',
                'cost_center' => 'INA Permata 1',
            ],
            [
                'id' => '37f80376-db4e-4106-8ef1-bb1bd709320a',
                'cost_center_code' => '9',
                'cost_center' => 'INA Permata 2',
            ],
            [
                'id' => '6c53bebc-3545-4d09-9b1f-85e03f84e68a',
                'cost_center_code' => '10',
                'cost_center' => 'KING Fisher',
            ],
            [
                'id' => 'ab7cf9b1-8484-4a52-b8e7-96294c890763',
                'cost_center_code' => '11',
                'cost_center' => 'Star Onix',
            ],
            [
                'id' => '01101635-73e4-424c-9d4f-da23757cad2f',
                'cost_center_code' => '12',
                'cost_center' => 'JSJB Jindi',
            ],
            [
                'id' => '3a071662-a0a8-4b59-946f-8b508ef5e5af',
                'cost_center_code' => '13',
                'cost_center' => 'CS GHJ',
            ],
        ];

        foreach ($data as $data) {
            CostCenter::create($data);
        }
    }
}
