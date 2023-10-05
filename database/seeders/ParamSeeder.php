<?php

namespace Database\Seeders;

use App\Models\Param;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ParamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'id' => '3f19237b-9d83-488e-8a98-07a6c934b68a',
                'parent_id' => NULL,
                'category' => 'unit',
                'param' => 'None',
                'slug' => NULL,
                'order' => NULL,
            ],
            [
                'id' => '68005489-3977-4607-95c2-8152dbd43177',
                'parent_id' => NULL,
                'category' => 'unit',
                'param' => 'KG',
                'slug' => NULL,
                'order' => NULL,
            ],
            [
                'id' => 'c2a55f69-3e12-441e-973e-8ced0794bf77',
                'parent_id' => NULL,
                'category' => 'unit',
                'param' => 'GRM',
                'slug' => NULL,
                'order' => NULL,
            ],
            [
                'id' => '4b0861a6-f1f7-4edc-a143-ab1b5d2628a8',
                'parent_id' => NULL,
                'category' => 'unit',
                'param' => 'TIN',
                'slug' => NULL,
                'order' => NULL,
            ],
            [
                'id' => 'fe9d2ce2-db00-4f91-89b7-77e06f5972b1',
                'parent_id' => NULL,
                'category' => 'unit',
                'param' => 'BTL',
                'slug' => NULL,
                'order' => NULL,
            ],
            [
                'id' => '019b5e53-fe9e-41b2-8ee4-551e78e6a43f',
                'parent_id' => NULL,
                'category' => 'unit',
                'param' => 'LTR',
                'slug' => NULL,
                'order' => NULL,
            ],
            [
                'id' => 'b08ed32e-f757-4afd-bd1e-e3955fb27afd',
                'parent_id' => NULL,
                'category' => 'unit',
                'param' => 'TUB',
                'slug' => NULL,
                'order' => NULL,
            ],
            [
                'id' => '90539cb1-9fd2-475e-9c1b-e844022ff898',
                'parent_id' => NULL,
                'category' => 'unit',
                'param' => 'BAG',
                'slug' => NULL,
                'order' => NULL,
            ],
            [
                'id' => 'ef698bd0-499e-47c8-b92d-15bf920816db',
                'parent_id' => NULL,
                'category' => 'unit',
                'param' => 'EA',
                'slug' => NULL,
                'order' => NULL,
            ],
            [
                'id' => '025d8dd2-afea-4e16-92a0-aa36f432d812',
                'parent_id' => NULL,
                'category' => 'unit',
                'param' => 'BOX',
                'slug' => NULL,
                'order' => NULL,
            ],
            [
                'id' => '8b49180e-c1b7-44a7-9368-53ac77a3a49c',
                'parent_id' => NULL,
                'category' => 'unit',
                'param' => 'CTN',
                'slug' => NULL,
                'order' => NULL,
            ],
            [
                'id' => 'ffdd6259-d02d-444d-a4e7-34619e2d27b8',
                'parent_id' => NULL,
                'category' => 'unit',
                'param' => 'GLN',
                'slug' => NULL,
                'order' => NULL,
            ],
            [
                'id' => 'f35a0abf-e37f-4d95-9430-437307c2fce9',
                'parent_id' => NULL,
                'category' => 'unit',
                'param' => 'ROLL',
                'slug' => NULL,
                'order' => NULL,
            ],
            [
                'id' => '9a7c1c51-c6cf-4a8b-bab4-6c5afab00daf',
                'parent_id' => NULL,
                'category' => 'unit',
                'param' => 'SLOP',
                'slug' => NULL,
                'order' => NULL,
            ],
            [
                'id' => '7a3f7ed5-99d6-4b0c-88d5-10404111c6e1',
                'parent_id' => NULL,
                'category' => 'unit',
                'param' => 'PPN',
                'slug' => NULL,
                'order' => NULL,
            ],
            [
                'id' => 'e521a128-ff49-4a4b-b189-7d8c72ccf0b5',
                'parent_id' => NULL,
                'category' => 'unit',
                'param' => 'SISIR',
                'slug' => NULL,
                'order' => NULL,
            ],
            [
                'id' => '2d1ba812-dacb-47db-9224-4a5f9d147353',
                'parent_id' => NULL,
                'category' => 'unit',
                'param' => 'LOT',
                'slug' => NULL,
                'order' => NULL,
            ],
        ];

        foreach ($data as $data) {
            Param::create($data);
        }
    }
}
