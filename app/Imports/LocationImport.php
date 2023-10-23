<?php

namespace App\Imports;

use App\Models\Location;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class LocationImport implements ToCollection, WithHeadingRow, WithValidation
{
    use Importable;

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) 
        {
            $location_id = NULL;
            if (!empty($row['parent_location_code'])) {
                $location_id = Location::where('location_code', $row['parent_location_code'])->first()->id;
            }
            Location::create([
                'location_code' => $row['location_code'],
                'location' => $row['location'],
                'parent_location_id' => $location_id,
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'location_code' => ['required', 'unique:locations,location_code', 'distinct'],
            'location' => ['required', 'unique:locations,location', 'distinct'],
            'parent_location_code' => ['nullable'],
        ];
    }
}
