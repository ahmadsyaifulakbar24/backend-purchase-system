<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MorExport implements FromView, ShouldAutoSize, WithStyles
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'alignment' => [
                    'horizontal' => 'center',
                    'vertical' => 'center',
                ]
            ], // Center align row 1
            2 => [
                'alignment' => [
                    'horizontal' => 'center',
                    'vertical' => 'center',
                ]
            ], // Center align row 2
            3 => [
                'alignment' => [
                    'horizontal' => 'center',
                    'vertical' => 'center',
                ]
            ], // Center align row 3
            4 => [
                'alignment' => [
                    'horizontal' => 'center',
                    'vertical' => 'center',
                ]
            ], // Center align row 4
            5 => [
                'alignment' => [
                    'horizontal' => 'center',
                    'vertical' => 'center',
                ]
            ], // Center align row 5
            6 => [
                'alignment' => [
                    'horizontal' => 'center',
                    'vertical' => 'center',
                ]
            ], // Center align row 6
            7 => [
                'alignment' => [
                    'horizontal' => 'center',
                    'vertical' => 'center',
                ]
            ], // Center align row 7
            8 => [
                'alignment' => [
                    'horizontal' => 'center',
                    'vertical' => 'center',
                ]
            ], // Center align row 8
            9 => [
                'alignment' => [
                    'horizontal' => 'center',
                    'vertical' => 'center',
                ]
            ], // Center align row 9
        ];
    }

    public function view(): View
    {
        return view('exports.mor', [
            'location' => $this->data['location'],
            'month' => $this->data['month'],
            'mm' => $this->data['mm'],
            'year' => $this->data['year'],
            'last_date' => $this->data['last_date'],
            'item_product' => $this->data['item_product'],
        ]);
    }
}
