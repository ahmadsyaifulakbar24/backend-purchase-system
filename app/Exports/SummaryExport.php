<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SummaryExport implements FromView, ShouldAutoSize, WithStyles
{
    // protected $year, $month_name, $data;

    // public function __construct($year, $month_name, $data)
    // {
    //     $this->year = $year;
    //     $this->month_name = $month_name;
    //     $this->data = $data;
    // }

    public function styles(Worksheet $sheet) 
    {
        $sheet->getStyle('A3:E5')->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $sheet->getStyle('A8:E9')->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'font' => [
                'italic' => true,
            ]
        ]);

        $sheet->getStyle('A12:E12')->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'font' => [
                'bold' => true,
            ]
        ]);
    }

    public function view(): View
    {
        // $data = [
        //     'year' => $this->year,
        //     'month_name' => $this->month_name,
        //     'data' => $this->data,
        // ]
        $data = [];
        return view('exports.summary_excel', $data);
    }
}
