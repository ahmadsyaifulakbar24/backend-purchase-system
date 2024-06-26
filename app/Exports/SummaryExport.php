<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SummaryExport implements FromView, ShouldAutoSize, WithStyles
{
    protected $year, $month_name, $data, $data_length;

    public function __construct($year, $month_name, $data)
    {
        $this->year = $year;
        $this->month_name = $month_name;
        $this->data = $data;
        $this->data_length = strval(count($data) + 10);
    }

    public function styles(Worksheet $sheet) 
    {
        $sheet->getStyle('A1:E9')->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
        $sheet->getStyle('A6:E7')->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'A9D08E',
                ],
            ],
            'font' => [
                'bold' => true,
            ],
        ]);
        $sheet->getStyle('A9:E9')->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'F4B084',
                ],
            ],
            'font' => [
                'bold' => true,
            ],
        ]);
        $sheet->getStyle('A'. $this->data_length. ':E'. $this->data_length)->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => '757171',
                ],
            ],
            'font' => [
                'bold' => true,
                'color' => [ 
                    'rgb' => 'FFFFFF',
                ],
            ],
        ]);
    }

    public function view(): View
    {
        $data = [
            'year' => $this->year,
            'month_name' => $this->month_name,
            'data' => $this->data,
        ];

        return view('exports.summary_excel', $data);
    }
}
