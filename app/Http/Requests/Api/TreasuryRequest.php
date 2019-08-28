<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\JsonRequest;

class TreasuryRequest extends JsonRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true; //in_array($this->getClientIp(), config('treasury.allowed_ips'), true);
    }
}
