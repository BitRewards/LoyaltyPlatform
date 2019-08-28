<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\AbstractBonusRequest;
use App\Http\Requests\Traits\UserByKey;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GiveBonusRequest extends AbstractBonusRequest
{
    use UserByKey;

    /**
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('partner-or-cashier');
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'bonus' => 'required|integer|min:1',
            'comment' => 'string',
            'action_id' => 'integer',
        ];
    }

    /**
     * @return array
     */
    public function messages()
    {
        return [
            'bonus.required' => __('Fill the field'),
        ];
    }

    /**
     * Get bonus receiver.
     *
     * @return \App\Models\User
     */
    public function bonusReceiver()
    {
        $user = User::where('key', $this->route('userKey'))->first();

        if (is_null($user) || $user->partner->id !== $this->user()->partner->id) {
            throw new NotFoundHttpException('Bonus receiver was not found');
        }

        return $user;
    }
}
