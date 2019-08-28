<?php

namespace App\Traits;

use App\Services\ImageStorageService\IImageStorage;
use Illuminate\Http\UploadedFile;

trait ImageAttributeTrait
{
    public function setImageAttribute(UploadedFile $value)
    {
        /**
         * @var IImageStorage
         */
        $imageStorageService = \App::make(IImageStorage::class);
        $path = $this->getUploadPath();

        // New image
        if (null === $this->image_url && $value) {
            $this->image_url = $imageStorageService->upload($value, $path);
        } elseif (null !== $this->image_url && $value) {
            // Replace image
            $imageStorageService->delete($this->image_url);
            $this->image_url = $imageStorageService->upload($value, $path);
        } elseif (null !== $this->image_url && !$value) {
            // Delete image
            $imageStorageService->delete($this->image_url);
            $this->image_url = null;
        }
    }
}
