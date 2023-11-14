<?php
namespace App\Helpers;

use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class ExcelHelper {

    public static function read($file)
    {
        $excel = Excel::toArray([], $file);
        $data = $excel[0];
        $headers = array_shift($data);
        
        $result = [];
        foreach($data as $row) {
            foreach($row as $index => $value) {

                if (in_array($headers[$index], ['date'])) {
                    $value = Carbon::parse(Date::excelToDateTimeObject($value))->format("Y-m-d");
                }

                $row_value[$headers[$index]] = $value;
            }
            
            $result[] = $row_value;
        }
        return $result;
    }
}