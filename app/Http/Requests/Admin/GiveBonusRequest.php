<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\AbstractBonusRequest;
use App\Models\Partner;
use App\Models\User;

/**
 * @property Partner $partner
 * @property int     $user_id
 * @property int     $bonus
 */
class GiveBonusRequest extends AbstractBonusRequest
{
    private $user;

    /**
     * Get bonus receiver.
     *
     * @return \App\Models\User
     */
    public function bonusReceiver()
    {
        return $this->getUser();
    }

    /**
     * @return bool
     */
    public function authorize()
    {
        $this->user = User::find($this->user_id);

        $canGiveBonus = $this->user && \Auth::user()->can('update', $this->user);

        return $canGiveBonus;
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'user_id' => 'required|integer',
        ] + parent::rules();
    }

    /**
     * @return \App\Models\User
     */
    public function getUser()
    {
        return $this->user;
    }
}
