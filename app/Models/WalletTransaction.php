<?php

namespace App\Models;

use Backpack\CRUD\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    use CrudTrait;

    protected $primaryKey = 'hash';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'created_at',
        'hash',
        'blockNumber',
        'from',
        'to',
        'amount',
        'amount_with_name',
        'status',
    ];
}
