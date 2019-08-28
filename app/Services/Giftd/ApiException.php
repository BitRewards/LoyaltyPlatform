<?php

namespace App\Services\Giftd;

class ApiException extends \Exception
{
    public $code;
    public $data;

    protected function _stringifyData($data)
    {
        if (!is_array($data)) {
            return $data;
        }

        return \HJson::encode($data);
    }

    public function __construct($data, $code = null)
    {
        parent::__construct($this->_stringifyData($data));
        $this->code = $code;
        $this->data = $data;
    }

    public function __toString()
    {
        return parent::__toString().($this->code ? '; code = '.$this->code : null);
    }
}
