<?php

namespace App\Http\Requests;

/**
 * @property string $ethereum_wallet
 */
class BitrewardsUpdateWalletRequest extends BaseFormRequest
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

        $input['ethereum_wallet'] = isset($input['ethereum_wallet']) ? trim(mb_strtolower($input['ethereum_wallet'])) : null;

        $this->replace($input);

        return $this->all();
    }

    public function rules()
    {
        return [
            'ethereum_wallet' => 'required|regex:/^0x[a-fA-F0-9]{40}$/u',
        ];
    }

    public function messages()
    {
        return [
            'ethereum_wallet.regex' => __('The value does not look like an Ethereum address'),
            'ethereum_wallet.required' => __('Enter the Ethereum address from which the translation will be made'),
        ];
    }
}
