<?php

namespace Database\Seeders;

use App\Models\Discount;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DiscountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'id' => '3296d884-ca48-4207-ab8f-319de262f8bc',
                'discount' => '5',
            ],
            [
                'id' => '5db5cf6e-0d8f-446f-92ae-3233432d2a5c',
                'discount' => '10',
            ],
            [
                'id' => '4032edaa-8362-401e-8a04-d72a46660db2',
                'discount' => '15',
            ],
        ];

        foreach($data as $data) {
            Discount::create($data);
        }
    }
}
