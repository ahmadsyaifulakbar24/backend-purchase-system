<?php
namespace App\Helpers;

use Maatwebsite\Excel\Facades\Excel;

class ExcelHelper {

    public static function read($file)
    {
        $excel = Excel::toArray([], $file);
        $data = $excel[0];
        $headers = array_shift($data);
        
        $result = [];
        foreach($data as $row) {
            foreach($row as $index => $value) {
                $row_value[$headers[$index]] = $value;
            }

            $result[] = $row_value;
        }
        return $result;
    }
}