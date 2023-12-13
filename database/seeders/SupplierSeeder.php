<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'code' => 'SP01',
                'type' => 'Gudang Pusat',
                'name' => 'TOKO SBL',
                'category' => 'Pusat',
                'npwp' => '11111',
                'contact_person' => 'Pusat',
                'address' => 'Pusat',
                'email' => 'pusat@pusat.com',
                'phone' => '087776555677',
                'main' => '1',
            ]
        ];

        foreach ($data as $data) {
            Supplier::create($data);
        }
    }
}
