<?php

namespace App\Http\Requests\Api\Rewards;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRewardRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('partner');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'string|max:255',
            'price' => 'integer',
            'price_type' => 'required|string|in:'.implode(',', \HReward::priceTypes()),
            'type' => 'string|in:'.implode(',', \HReward::types()),
            'value' => 'integer',
            'value_type' => 'string|in:'.implode(',', \HReward::valueTypes()),
            'status' => 'string|in:'.implode(',', \HReward::statuses()),
            'tag' => 'string|max:255',
            'description' => 'string',
            'description_short' => 'string|max:255',
            'config' => 'string',
            'partner_id' => 'integer|exists:partners,id',
        ];
    }
}
