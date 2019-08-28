<?php
/**
 * IImageStorage.php
 * Creator: lehadnk
 * Date: 30/07/2018.
 */

namespace App\Services\ImageStorageService;

use Illuminate\Http\UploadedFile;

interface IImageStorage
{
    public function upload(UploadedFile $file, string $path): string;

    public function delete(string $fileName): bool;
}
