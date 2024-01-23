<?php

namespace App\Traits;

trait FileProcessTrait
{

    public static function UploadImage(mixed $file, string $path): string{

        $fileName = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('uploads/' . $path), $fileName); //storage klasörü yapılandırılacak.
        return $fileName;

    }

    public static function UploadFile(mixed $file, string $path): string{

        $fileName = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('uploads/' . $path), $fileName); //storage klasörü yapılandırılacak.
        return $fileName;

    }

    public static function DeleteFile(string $path, string $fileName): bool{

        if(file_exists(public_path('uploads/' . $path . '/' . $fileName))) {
            unlink(public_path('uploads/' . $path . '/' . $fileName));
            return true;
        }
        return false;

    }


}
