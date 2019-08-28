<?php

namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;

class UsersImport implements ToModel
{
    /**
     * @param array $row
     *
     * @return User|null
     */
    public function model(array $row)
    {
        return new User([
            'name' => $row[0],
            'email' => $row[1],
            'phone' => $row[2],
            'balance' => $row[3],
        ]);
    }
}
