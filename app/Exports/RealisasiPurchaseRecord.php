<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RealisasiPurchaseRecord implements FromView, ShouldAutoSize, WithStyles
{
    public function styles(Worksheet $sheet) 
    {
        $sheet->getStyle('A2')->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
    }

    public function view(): View
    {
        $data = [];
        return view('exports.realisasi_purchase_record_excel', $data);
    }
}
