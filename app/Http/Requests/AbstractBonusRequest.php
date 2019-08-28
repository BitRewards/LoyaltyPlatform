<?php

namespace App\Http\Requests;

use App\DTO\CustomBonusData;
use App\Models\Action;
use Illuminate\Contracts\Support\MessageBag;

abstract class AbstractBonusRequest extends BaseFormRequest
{
    /**
     * @var Action|null
     */
    protected $bonusAction;

    /**
     * Get bonus receiver.
     *
     * @return \App\Models\User
     */
    abstract public function bonusReceiver();

    public function rules()
    {
        return [
            'bonus' => 'integer',
            'bonus_fiat' => 'amount',
            'comment' => 'string',
            'action_id' => 'integer',
        ];
    }

    public function doExtraValidation(MessageBag $messageBag)
    {
        if ($this->has('action_id') && (int) $this->get('action_id') && !$this->bonusAction()) {
            $messageBag->add('action_id', __('Linked Action was not found'));
        }

        if (!$this->get('bonus') && !$this->get('bonus_fiat')) {
            $messageBag->add('bonus', __('Either bonus or bonus_fiat parameter is required'));
        }
    }

    /**
     * Get bonus action.
     *
     * @return \App\Models\Action|null
     */
    public function bonusAction()
    {
        if (!$this->has('action_id')) {
            return null;
        }

        if (!$this->bonusAction && ($actionId = (int) ($this->get('action_id')))) {
            $this->bonusAction =
                Action
                    ::where('partner_id', $this->user()->partner_id)
                    ->where('id', $actionId)
                    ->first();
        }

        return $this->bonusAction;
    }

    public function getBonusAmount(): float
    {
        if ($this->get('bonus_fiat')) {
            $bonusFiat = (float) $this->get('bonus_fiat');

            return \HAmount::fiatToPoints($bonusFiat, $this->user()->partner);
        } else {
            return $this->request->getInt('bonus');
        }
    }

    public function getComment()
    {
        return $this->input('comment');
    }

    /**
     * Get request DTO.
     *
     * @return CustomBonusData
     */
    public function getDto()
    {
        return new CustomBonusData(
            $this->bonusReceiver(),
            $this->getBonusAmount(),
            $this->user(),
            $this->bonusAction(),
            null,
            $this->getComment()
        );
    }
}
