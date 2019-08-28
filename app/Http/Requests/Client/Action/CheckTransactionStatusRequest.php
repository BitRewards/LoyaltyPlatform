<?php

namespace App\Http\Requests\Client\Action;

use App\Http\Requests\BaseFormRequest;
use App\Models\Partner;

/**
 * @property int     $transaction_id
 * @property Partner $partner
 */
class CheckTransactionStatusRequest extends BaseFormRequest
{
    public function authorize()
    {
        return !\Auth::guest();
    }

    public function rules()
    {
        return [
            'transaction_id' => 'int',
        ];
    }
}
