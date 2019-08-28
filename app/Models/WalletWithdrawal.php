<?php

namespace App\Models;

use Backpack\CRUD\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class WalletWithdrawal extends Model
{
    use CrudTrait;

    protected $fillable = [
        'amount',
        'currency',
        'to',
    ];
}
