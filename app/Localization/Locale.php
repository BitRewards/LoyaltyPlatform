<?php

namespace App\Localization;

use Illuminate\Support\Collection;

class Locale extends Collection
{
    /**
     * @var string
     */
    protected $name;

    public function __construct(string $name, $items = [])
    {
        parent::__construct($items);

        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function jsonSerialize()
    {
        $result = [];

        /** @var Translate $translate */
        foreach ($this as $translate) {
            $result[$translate->getId()] = $translate->getTranslate();
        }

        return $result;
    }
}
