<?php

namespace App\Db;

use Illuminate\Database\Migrations\MigrationCreator;

class CrmMigrationCreator extends MigrationCreator
{
    public function getStubPath()
    {
        return __DIR__.'/stubs';
    }
}
