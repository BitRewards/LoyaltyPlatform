<?php

namespace Helper;

use Codeception\Module\Laravel5;
use Codeception\Module\REST;

class Functional extends \Codeception\Module
{
    protected const CSRF_META = 'meta[name=csrf-token]';

    protected function getLaravelModule(): Laravel5
    {
        return $this->getModule('Laravel5');
    }

    protected function getRESTModule(): REST
    {
        return $this->getModule('REST');
    }

    public function grabCSRFToken(): string
    {
        $this
            ->getLaravelModule()
            ->seeElement(self::CSRF_META);

        return $this
            ->getLaravelModule()
            ->grabAttributeFrom(self::CSRF_META, 'content');
    }

    public function useCSRFToken(): void
    {
        $this
            ->getRESTModule()
            ->haveHttpHeader('x-csrf-token', $this->grabCSRFToken());
    }
}
