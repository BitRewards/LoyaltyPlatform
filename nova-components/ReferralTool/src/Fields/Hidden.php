<?php

namespace Bitrewards\ReferralTool\Fields;

use Laravel\Nova\Fields\Field;

class Hidden extends Field
{
    public $showOnIndex = false;
    public $showOnDetail = false;

    public $component = 'hidden-field';

    public function withValue($value = null): self
    {
        $this->withMeta([
            'value' => $value,
        ]);

        return $this;
    }
}
