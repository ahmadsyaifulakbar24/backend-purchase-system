<?php

namespace App\Http\Controllers\API\MealSheet;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class MealSheetMonthlyController extends Controller
{
    public function monthly_meal_sheet_pdf()
    {
        $data = [];
        $pdf = Pdf::loadView('pdf.monthly_meal_sheet', $data);
        $file_name = 'monthly_meal_sheet-.pdf';
        return $pdf->download($file_name);
    }
}
