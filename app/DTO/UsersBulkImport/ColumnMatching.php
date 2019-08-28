<?php

namespace App\DTO\UsersBulkImport;

use App\DTO\DTO;

class ColumnMatching extends DTO
{
    const NOT_DETECTED = -1;

    public $email = self::NOT_DETECTED;
    public $phone = self::NOT_DETECTED;
    public $name = self::NOT_DETECTED;
    public $balance = self::NOT_DETECTED;

    /**
     * @param array $columns
     *
     * @return $this
     */
    public function __construct(array $columns)
    {
        $this->detect($columns);

        return $this;
    }

    /**
     * @param array $columns
     *
     * @return $this
     */
    public function detect(array $columns)
    {
        $this->detectBalance($columns);
        $this->detectEmail($columns);
        $this->detectPhone($columns);
        $this->detectName($columns);

        return $this;
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return $this->balance != static::NOT_DETECTED
        && ($this->phone != static::NOT_DETECTED || $this->email != static::NOT_DETECTED);
    }

    /**
     * @param array $columns
     *
     * @return $this
     */
    public function detectBalance(array $columns)
    {
        $this->balance = ColumnMatching::NOT_DETECTED;

        if ($columns) {
            $balanceIndex = count($columns) - 1;
            $hasBalanceColumn = is_numeric($columns[$balanceIndex]);

            if ($hasBalanceColumn) {
                $this->balance = $balanceIndex;
            }
        }

        return $this;
    }

    /**
     * @param array $columns
     *
     * @return $this
     */
    public function detectEmail(array $columns)
    {
        $this->email = ColumnMatching::NOT_DETECTED;

        foreach ($columns as $index => $column) {
            if (filter_var($column, FILTER_VALIDATE_EMAIL)) {
                $this->email = $index;
            }
        }

        return $this;
    }

    /**
     * @param array $columns
     *
     * @return $this
     */
    public function detectPhone(array $columns)
    {
        $this->phone = ColumnMatching::NOT_DETECTED;

        foreach ($columns as $index => $column) {
            if (\HStr::isPhone($column)) {
                $this->phone = $index;
            }
        }

        return $this;
    }

    /**
     * @param $columns
     * @param $matching
     *
     * @return $this
     */
    public function detectName(array $columns)
    {
        $this->name = ColumnMatching::NOT_DETECTED;

        $detectedColumns = array_values($this->toArray());

        foreach ($columns as $index => $column) {
            if (!in_array($index, $detectedColumns)) {
                $this->name = $index;
            }
        }

        return $this;
    }
}
