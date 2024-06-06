<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RealMORExport implements FromView, ShouldAutoSize, WithStyles
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function styles(Worksheet $sheet) 
    {
        $sheet->getStyle('A2:A7')->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'font' => [
                'bold' => true,
                'size' => '18'
            ],
        ]);
        $sheet->getStyle('A2')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => '20'
            ],
        ]);
        $sheet->getStyle('A3')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => '14'
            ],
        ]);
        $sheet->getStyle('A5:A6')->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => '8EA9DB',
                ],
            ],
            'font' => [
                'bold' => true,
                'color' => [ 
                    'rgb' => 'FFFFFF',
                ],
            ],
        ]);
        $sheet->getStyle(8)->applyFromArray([
            'font' => [
                'bold' => true,
            ],
        ]);
    }

    public function view(): View
    {
        $data = $this->data;
        return view('exports.real_mor_excel', $data);
    }
}
