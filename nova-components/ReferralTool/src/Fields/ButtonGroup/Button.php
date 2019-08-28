<?php

namespace Bitrewards\ReferralTool\Fields\ButtonGroup;

class Button
{
    /**
     * @var callable|null
     */
    protected $label;

    /**
     * @var callable|null
     */
    protected $action;

    /**
     * @var bool|callable
     */
    protected $disabled = false;

    public function __construct($label, $action = null, $disabled = false)
    {
        $this->setLabel($label);
        $this->setAction($action);
        $this->disable($disabled);
    }

    public function setLabel($label): self
    {
        $this->label = $label;

        return $this;
    }

    public function setAction($action): self
    {
        if (null === $action || is_callable($action)) {
            $this->action = $action;
        } else {
            $this->action = function () use ($action) {
                return $action;
            };
        }

        return $this;
    }

    public function disable($flag): self
    {
        if (null === $flag || is_callable($flag)) {
            $this->disabled = $flag;
        } else {
            $this->disabled = function () use ($flag) {
                return $flag;
            };
        }

        return $this;
    }

    public function getData($context = null): array
    {
        return [
            'label' => $this->label,
            'action' => is_callable($this->action) ? call_user_func($this->action, $context) : $this->action,
            'disabled' => (bool) (is_callable($this->disabled) ? call_user_func($this->disabled, $context) : $this->disabled),
        ];
    }
}
