<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RealMORExport implements FromView, ShouldAutoSize, WithStyles
{
    public function styles(Worksheet $sheet) 
    {
       
    }

    public function view(): View
    {
        $data = [];
        return view('exports.real_mor_excel', $data);
    }
}
