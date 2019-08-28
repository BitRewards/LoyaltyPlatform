<?php

namespace Bitrewards\ReferralTool\Cards;

use Laravel\Nova\Card;

class SimpleTable extends Card
{
    public $width = 'full';

    /**
     * @var string|null
     */
    protected $title;

    /**
     * @var callable|array
     */
    protected $data = [];

    /**
     * @var string
     */
    protected $dataUrl;

    /**
     * @var array
     */
    protected $headers = [];

    public function __construct(?string $tableTitle = null)
    {
        parent::__construct('SimpleTable');

        $this->title = $tableTitle;
    }

    public function setInitialData($dataOrCallback): self
    {
        $this->data = $dataOrCallback;

        return $this;
    }

    public function setDataUrl(string $dataUrl): self
    {
        $this->dataUrl = '/'.ltrim($dataUrl, '/');

        return $this;
    }

    public function setHeaders(array $headers): self
    {
        $this->headers = $headers;

        return $this;
    }

    protected function getInitialData(): array
    {
        if (is_callable($this->data)) {
            return ($this->data)(request());
        }

        return $this->data;
    }

    public function jsonSerialize()
    {
        $this->withMeta([
            'title' => $this->title,
            'headers' => $this->headers,
            'dataUrl' => $this->dataUrl,
            'data' => $this->getInitialData(),
        ]);

        return parent::jsonSerialize();
    }
}
