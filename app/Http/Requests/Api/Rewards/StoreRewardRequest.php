<?php

namespace App\Http\Requests\Api\Rewards;

use App\Models\Reward;
use Illuminate\Foundation\Http\FormRequest;

class StoreRewardRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('create', Reward::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'price' => 'required|integer',
            'price_type' => 'required|string|in:'.implode(',', \HReward::priceTypes()),
            'type' => 'required|string|in:'.implode(',', \HReward::types()),
            'value' => 'required|integer',
            'value_type' => 'required|string|in:'.implode(',', \HReward::valueTypes()),
            'status' => 'required|string|in:'.implode(',', \HReward::statuses()),
            'tag' => 'string|max:255',
            'description' => 'string',
            'description_short' => 'string|max:255',
            'config' => 'string',
        ];
    }
}
