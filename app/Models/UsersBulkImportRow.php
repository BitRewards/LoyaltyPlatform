<?php

namespace App\Models;

/**
 * Class BulkImport.
 *
 * @property int             $id
 * @property int             $users_bulk_import_id
 * @property string          $data
 * @property bool            $is_skipped
 * @property bool            $is_new_user
 * @property bool            $is_exsiting_user
 * @property \Carbon\Carbon  $processed_at
 * @property UsersBulkImport $usersBulkImport
 */
class UsersBulkImportRow extends AbstractModel
{
    protected $table = 'users_bulk_imports_rows';

    protected $casts = [
        'processed_at' => 'datetime',
    ];

    public function usersBulkImport()
    {
        return $this->belongsTo(UsersBulkImport::class);
    }
}
