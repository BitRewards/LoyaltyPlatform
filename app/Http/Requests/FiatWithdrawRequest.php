<?php

namespace App\Http\Requests;

use App\Models\Partner;
use Illuminate\Contracts\Support\MessageBag;

/**
 * @property string  $cardNumber
 * @property string  $firstName
 * @property string  $secondName
 * @property float   $withdrawAmount
 * @property Partner $partner
 */
class FiatWithdrawRequest extends BaseFormRequest
{
    public function authorize()
    {
        return !\Auth::guest();
    }

    public function rules()
    {
        return [
            'cardNumber' => 'alpha_num|min:16|max:16',
            'firstName' => 'required|string',
            'secondName' => 'required|string',
            'withdrawAmount' => 'required',
        ];
    }

    public function doExtraValidation(MessageBag $messageBag)
    {
        $minAmount = $this->partner->getFiatWithdrawMinAmount();
        $maxAmount = $this->partner->getFiatWithdrawMaxAmount();

        if ($minAmount && ($this->withdrawAmount < $minAmount)) {
            $messageBag->add('withdrawAmount', __('The minimal withdraw is %minAmount%', [
                'minAmount' => $minAmount,
            ]));
        }

        if ($maxAmount && ($this->withdrawAmount > $maxAmount)) {
            $messageBag->add('withdrawAmount', __('The maximum withdraw is %maxAmount%', [
                'maxAmount' => $maxAmount,
            ]));
        }
    }
}
