<?php

namespace App\Localization;

class Translate
{
    /**
     * @var string
     */
    protected $locale;

    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $translate;

    public function __construct(string $locale, string $id, string $translate)
    {
        $this->locale = $locale;
        $this->id = $id;
        $this->translate = $translate;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTranslate(): string
    {
        return $this->translate;
    }

    public function __toString()
    {
        return $this->getTranslate();
    }
}
