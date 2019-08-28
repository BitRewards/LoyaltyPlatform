<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;

trait SelectelImageUploadsTrait
{
    /**
     * @param UploadedFile|null $value
     * @param $attribute_name
     * @param $disk
     * @param $destination_path
     * @param array $resize = []
     */
    public function resizeAndUpload($value, $attribute_name, $disk, $destination_path, array $resize = [])
    {
        $request = \Request::instance();

        // if a new file is uploaded, delete the file from the disk
        if ($request->hasFile($attribute_name) &&
            $this->{$attribute_name} &&
            null != $this->{$attribute_name}) {
            \Storage::disk($disk)->delete($this->{$attribute_name});
            $this->attributes[$attribute_name] = null;
        }

        // if the file input is empty, delete the file from the disk
        if (is_null($value) && null != $this->{$attribute_name}) {
            \Storage::disk($disk)->delete($this->{$attribute_name});
            $this->attributes[$attribute_name] = null;
        }

        // if a new file is uploaded, store it on disk and its filename in the database
        if ($request->hasFile($attribute_name) && $request->file($attribute_name)->isValid()) {
            $file = $request->file($attribute_name);
            $tempName = storage_path(md5(str_random(12).time()).'.'.$file->getClientOriginalExtension());
            $image = $this->resizeUploadedFile($file, $tempName, $resize);

            // 1. Generate a new file name
            $new_file_name = md5($file->getClientOriginalName().time()).'.'.$file->getClientOriginalExtension();
            $path = rtrim($destination_path, '/').'/'.$new_file_name;

            // 2. Move the new file to the correct path
            \Storage::disk($disk)->put($path, $image->stream()->__toString());

            // 3. Save the complete path to the database
            $this->attributes[$attribute_name] = \Storage::disk($disk)->url($path);

            // 4. Free memory & remove temp file
            $image->destroy();
            unlink($tempName);
        }
    }

    /**
     * @param UploadedFile $file
     * @param string       $tempName
     * @param array        $options  = []
     *
     * @return \Intervention\Image\Image|null
     */
    protected function resizeUploadedFile(UploadedFile $file, string $tempName, array $options = [])
    {
        $image = \Image::make($file->getRealPath());

        if (!count($options)) {
            return $image;
        }

        $image->resize($options['width'] ?? null, $options['height'] ?? null, function ($constraint) use ($options) {
            if (!empty($options['aspect_ratio'])) {
                $constraint->aspectRatio();
            }

            if (!empty($options['upsize'])) {
                $constraint->upsize();
            }
        })->save($tempName);

        return $image;
    }
}
