<?php

namespace App\Models;

/**
 * Class BulkImport.
 *
 * @property int            $id
 * @property int            $count
 * @property string         $mode
 * @property string         $title
 * @property int            $partner_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property Partner        $partner
 */
class UsersBulkImport extends AbstractModel
{
    protected $table = 'users_bulk_imports';

    /**
     * @return Partner
     */
    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function usersBulkImportRows()
    {
        return $this->hasMany(UsersBulkImportRow::class);
    }
}
