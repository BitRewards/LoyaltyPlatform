<?php

namespace App\Models;

/**
 * Class Migration.
 *
 * @property string $migration
 * @property int    $batch
 *
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Migration whereMigration($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Migration whereBatch($value)
 * @mixin \Eloquent
 */
class Migration extends AbstractModel
{
    protected $table = 'migrations';

    public $timestamps = false;

    protected $fillable = [
        'migration',
        'batch',
    ];

    protected $guarded = [];
}
