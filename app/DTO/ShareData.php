<?php

namespace App\DTO;

class ShareData extends StoreEntityData
{
    const TYPE_INSTAGRAM = 'instagram';
    const TYPE_TELEGRAM = 'telegram';
    const TYPE_CUSTOM = 'custom';

    public $url;

    public $image_url;

    public $type;
}
