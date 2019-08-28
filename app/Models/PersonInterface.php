<?php

namespace App\Models;

use Illuminate\Support\Collection;

interface PersonInterface
{
    public function getPersonUsers(): Collection;

    public function getPrimaryEmail(): ?string;
}
