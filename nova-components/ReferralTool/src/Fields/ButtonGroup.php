<?php

namespace Bitrewards\ReferralTool\Fields;

use Bitrewards\ReferralTool\Fields\ButtonGroup\Button;
use Laravel\Nova\Fields\Field;

class ButtonGroup extends Field
{
    public $component = 'button-group';

    /**
     * @var Button[]
     */
    protected $buttons = [];

    public function __construct($buttons = null)
    {
        parent::__construct(__('Actions'));

        if (!is_array($buttons)) {
            $buttons = \func_get_args();
        }

        foreach ($buttons as $button) {
            $this->addButton($button);
        }
    }

    public static function button($label, $actionUrl = null, $disabled = false): Button
    {
        return new Button($label, $actionUrl, $disabled);
    }

    public function addButton(Button $button): self
    {
        $this->buttons[] = $button;

        return $this;
    }

    public function resolve($resource, $attribute = null): void
    {
        $buttonsData = [];

        foreach ($this->buttons as $button) {
            $buttonsData[] = $button->getData($resource);
        }

        $this->withMeta([
            'buttons' => $buttonsData,
        ]);
    }

    public function asHtml()
    {
        return $this->withMeta(['asHtml' => true]);
    }
}
