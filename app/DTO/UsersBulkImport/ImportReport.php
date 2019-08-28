<?php

namespace App\DTO\UsersBulkImport;

use App\DTO\DTO;

class ImportReport extends DTO
{
    const SKIPPED = 'skipped';
    const UPDATED = 'updated';
    const CREATED = 'created';

    public $total = 0;
    public $skipped = 0;
    public $updated = 0;
    public $created = 0;

    public function update($key, $value = 1)
    {
        if (isset($this->$key)) {
            ++$this->total;
            $this->$key += $value;
        }

        return $this;
    }
}
