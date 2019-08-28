<?php

namespace App\Http\Requests;

use App\Models\Partner;

/**
 * @property Partner $partner
 * @property string  $phone
 */
class BitrewardsPayoutRequest extends BaseFormRequest
{
    public function authorize()
    {
        return !\Auth::guest();
    }

    /**
     * Validate the input.
     *
     * @param \Illuminate\Validation\Factory $factory
     *
     * @return \Illuminate\Validation\Validator
     */
    public function validator($factory)
    {
        return $factory->make(
            $this->sanitizeInput(), $this->container->call([$this, 'rules']), $this->messages()
        );
    }

    protected function sanitizeInput()
    {
        $input = $this->all();

        $input['withdraw_eth'] = isset($input['withdraw_eth']) ? trim(mb_strtolower($input['withdraw_eth'])) : null;

        $this->replace($input);

        return $this->all();
    }

    public function rules()
    {
        return [
            'withdraw_eth' => 'required|regex:/^0x[a-fA-F0-9]{40}$/u',
            'token_amount' => 'required|int|min:1',
        ];
    }

    public function messages()
    {
        return [
            'withdraw_eth.regex' => __('The value does not look like an Ethereum address'),
            'withdraw_eth.required' => __('Enter Ethereum address which should be used for payout'),
            'token_amount.required' => __('Enter amount of BIT for transfer'),
        ];
    }
}
