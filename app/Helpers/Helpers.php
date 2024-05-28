<?php

if (!function_exists('formatRupiah')) {
    function formatRupiah($angka) {
        if(!empty($angka)) {
            return 'Rp. ' . number_format($angka, 0);
        } else {
            return '';
        }
    }
}

if (!function_exists('numberFormat')) {
    function numberFormat($angka) {
        if(!empty($angka)) {
            if (is_numeric($angka) && strpos($angka, '.') !== false) {
                $format_angka = number_format($angka, 5);
                return rtrim($format_angka, '0');
            } else {
                return number_format($angka);
            }
        } else {
            return '';
        }
    }
}

if (!function_exists('indexToAlphabet')) {
    function indexToAlphabet($index) {
        $abjad = range('A', 'Z');
        
        $loop = floor($index / 26);
        $index_abjad = ($index % 26);
        
        $result = "";
        for ($i = 0; $i <= $loop; $i++) {
              if($i > 0) {
                 $result = indexToAlphabet($i - 1);
              }
              $result .= $abjad[$index_abjad];
        }
        
        return $result;
    }
}

if (!function_exists('numericToRoman')) {
    function numericToRoman($number)
    {
        $map = [
            'M' => 1000, 
            'CM' => 900, 
            'D' => 500, 
            'CD' => 400, 
            'C' => 100, 
            'XC' => 90, 
            'L' => 50, 
            'XL' => 40, 
            'X' => 10, 
            'IX' => 9, 
            'V' => 5, 
            'IV' => 4, 
            'I' => 1
        ];
        $returnValue = '';
        while ($number > 0) {
            foreach ($map as $roman => $int) {
                if($number >= $int) {
                    $number -= $int;
                    $returnValue .= $roman;
                    break;
                }
            }
        }
        return $returnValue;
    }
}