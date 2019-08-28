<?php

namespace App\DTO;

class OrderData extends StoreEntityData
{
    public $amountTotal;
    public $isPaid;
    public $isDelivered;

    public $comment;
    public $managerComment;

    public $predefinedCashback;
    public $predefinedReferrerCashback;

    // array of string promo codes
    public $promoCodes;

    // array of arrays looking like ['product_id' => 52510596, 'quantity' => 1, 'total_price' => 950, 'title' => "Нож для выживания H2034, Viking Nordway"]
    public $orderLines;
}
