<?php

namespace Bitrewards\ReferralTool\Cards;

use Laravel\Nova\Card;

class StaticValue extends Card
{
    public function __construct(string $title, string $value)
    {
        parent::__construct('StaticValue');

        $this->withMeta([
            'title' => $title,
            'value' => $value,
        ]);
    }
}
