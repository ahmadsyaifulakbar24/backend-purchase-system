<?php

namespace App\Imports;

use App\Models\Client;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ClientImport implements ToCollection, WithHeadingRow, WithValidation
{
    use Importable;

    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) 
        {
            Client::create([
                'client_name' => $row['client_name'],
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'client_name' => ['required', 'string', 'unique:clients,client_name', 'distinct'],
        ];
    }
}
