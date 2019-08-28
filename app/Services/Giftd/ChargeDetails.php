<?php

namespace App\Services\Giftd;

/**
 * @property string $token
 * @property string $external_id
 * @property int    $time
 * @property float  $amount
 * @property float  $amount_total
 * @property float  $amount_left
 * @property string $type
 * @property string $comment
 */
class ChargeDetails
{
    const TYPE_MANUAL = 'manual';
    const TYPE_API = 'api';

    public $token;
    public $external_id;
    public $time;
    public $amount;
    public $amount_total;
    public $amount_left;
    public $type;
    public $comment;
}
