<?php

namespace App\Crud;

use App\Crud\Traits\ExtraFakeFields;
use Backpack\CRUD\CrudPanel as BaseCrudPanel;
use App\Crud\Traits\Search as ExtraSearch;
use App\Crud\Traits\Read as ExtraRead;

class CrudPanel extends BaseCrudPanel
{
    use ExtraSearch, ExtraRead, ExtraFakeFields;

    /**
     * @var callable
     */
    private $rowCounter;

    public function setRowsCounter(callable $counter): void
    {
        $this->rowCounter = $counter;
    }

    public function count(): int
    {
        if (\is_callable($this->rowCounter)) {
            return \call_user_func($this->rowCounter, $this->query);
        }

        return parent::count();
    }
}
