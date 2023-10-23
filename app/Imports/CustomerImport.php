<?php

namespace App\Imports;

use App\Models\Customer;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class CustomerImport implements ToCollection, WithHeadingRow, WithValidation
{
    use Importable;
    
    /**
    * @param Collection $rows
    */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row)
        {
            Customer::create([
                'code' => $row['code'],
                'name' => $row['name'],
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
            'code' => ['required', 'string', 'unique:customers,code', 'distinct'],
            'name' => ['required', 'string'],
            'contact_person' => ['required', 'string'],
            'address' => ['required', 'string'],
            'email' => ['required', 'email'],
            'phone' => ['required', 'numeric'],
        ];
    }
}
