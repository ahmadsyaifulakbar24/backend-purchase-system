<?php

namespace App\Imports;

use App\Models\Supplier;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class SupplierImport implements ToCollection, WithHeadingRow, WithValidation
{
    use Importable;
    
    /**
    * @param Collection $rows
    */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) 
        {
            Supplier::create([
                'code' => $row['code'],
                'type' => $row['type'],
                'name' => $row['name'],
                'category' => $row['category'],
                'npwp' => $row['npwp'],
                'contact_person' => $row['contact_person'],
                'address' => $row['address'],
                'email' => $row['email'],
                'phone' => $row['phone'],
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'unique:suppliers,code', 'distinct'],
            'type' => ['required', 'string'],
            'name' => ['required', 'string'],
            'category' => ['required', 'string'],
            'npwp' => ['required', 'string'],
            'contact_person' => ['required', 'string'],
            'address' => ['required', 'string'],
            'email' => ['required', 'email'],
            'phone' => ['required', 'numeric'],
        ];
    }
}
