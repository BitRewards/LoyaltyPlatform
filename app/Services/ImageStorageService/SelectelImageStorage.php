<?php
/**
 * SelectelImageStorage.php
 * Creator: lehadnk
 * Date: 30/07/2018.
 */

namespace App\Services\ImageStorageService;

use Illuminate\Http\UploadedFile;

class SelectelImageStorage implements IImageStorage
{
    private $disk = 'selectel';

    public function upload(UploadedFile $file, string $path): string
    {
        $fileName = rtrim($path, '/').'/'.$this->generateUniqueName($file);
        $image = \Image::make($file->getRealPath());
        \Storage::disk($this->disk)->put($fileName, $image->stream()->__toString());

        return \Storage::disk($this->disk)->url($fileName);
    }

    public function delete(string $fileName): bool
    {
        \Storage::disk($this->disk)->delete($fileName);

        return true;
    }

    private function generateUniqueName(UploadedFile $file): string
    {
        return md5(str_random(12).time()).'.'.$file->getClientOriginalExtension();
    }
}
