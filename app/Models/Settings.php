<?php

namespace App\Models;

/**
 * @property string $namespace
 * @property array  $options
 */
class Settings extends AbstractModel
{
    protected $table = 'settings';

    protected $fillable = [
        'namespace',
        'options',
    ];

    protected $casts = [
        'options' => 'array',
    ];

    protected $primaryKey = 'namespace';

    public $timestamps = true;
}
