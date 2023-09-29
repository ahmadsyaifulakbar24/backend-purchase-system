<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Image;

class FileHelpers {
    public static function upload_file(
        $path, 
        $file, 
        $disk = 'public', 
        $name = true
    )
    {
        $couter = 0;
        $name_of_upload = $file->getClientOriginalName();
        $original_name = pathinfo($name_of_upload, PATHINFO_FILENAME);
        $ext = $file->getClientOriginalExtension();

        if($file->isValid()) {
            if($name) {
                while(Storage::disk($disk)->exists($path.'/'.$name_of_upload)) {
                    $couter++;
                    $name_of_upload = $original_name." (".$couter.").".$ext;
                }
                $path = $file->storeAs($path, $name_of_upload, $disk);
            } else {
                $path = $file->store($path, $disk);
            }
            return $path;
        }
    }

    public static function file_name($path, $file)
    {
        $couter = 0;
        $name_of_upload = $file->getClientOriginalName();
        $original_name = pathinfo($name_of_upload, PATHINFO_FILENAME);
        $ext = $file->getClientOriginalExtension();
        while(Storage::disk('public')->exists($path.'/'.$name_of_upload)) {
            $couter++;
            $name_of_upload = $original_name." (".$couter.").".$ext;
        }
        return $name_of_upload;
    }
}