<?php

namespace App\Imports;

use App\Models\Department;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class DepartmentImport implements ToCollection, WithHeadingRow, WithValidation
{
    use Importable;
    
    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) 
        {
            Department::create([
                'department_code' => $row['department_code'],
                'department' => $row['department'],
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'department_code' => ['required', 'unique:departments,department_code', 'distinct'],
            'department' => ['required', 'unique:departments,department', 'distinct']
        ];
    }
}
