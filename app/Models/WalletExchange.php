<?php

namespace App\Models;

use Backpack\CRUD\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class WalletExchange extends Model
{
    use CrudTrait;

    protected $fillable = [
        'amount',
        'address',
        'wallet',
    ];
}
