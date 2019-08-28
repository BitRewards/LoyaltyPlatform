<?php

namespace App\Models;

use Backpack\CRUD\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class WalletSettings extends Model
{
    use CrudTrait;

    protected $fillable = [
        'fee_type',
        'fee',
        'min_withdrawal',
    ];
}
