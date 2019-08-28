<?php

namespace App\Http\Requests;

use App\Models\Partner;

/**
 * @property Partner $partner
 * @property string  $phone
 */
class BitrewardsDepositRequest extends BaseFormRequest
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

        $input['deposit_magic'] = isset($input['deposit_magic']) ? preg_replace('/[^0-9]/', '', $input['deposit_magic']) : null;

        $this->replace($input);

        return $this->all();
    }

    public function rules()
    {
        return [
            'deposit_magic' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'deposit_magic.required' => __('Enter Magic Number'),
        ];
    }
}
