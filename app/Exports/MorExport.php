<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class MorExport implements FromView, ShouldAutoSize
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        return view('exports.mor', [
            'location' => $this->data['location'],
            'month' => $this->data['month'],
            'mm' => $this->data['mm'],
            'year' => $this->data['year'],
            'item_product' => $this->data['item_product'],
        ]);
    }
}
