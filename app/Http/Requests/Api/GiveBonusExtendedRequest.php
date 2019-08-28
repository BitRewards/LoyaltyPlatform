<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\AbstractBonusRequest;
use App\Models\User;
use Illuminate\Contracts\Support\MessageBag;

class GiveBonusExtendedRequest extends AbstractBonusRequest
{
    /**
     * @var User|null
     */
    private $user;

    public function authorize(): bool
    {
        return $this->user()->can('partner-or-cashier');
    }

    public function rules(): array
    {
        return [
            'user_key' => 'string',
            'email' => 'string|email',
            'phone' => 'string',
            'auto_create' => 'bool',
            'name' => 'string',
        ] + parent::rules();
    }

    public function doExtraValidation(MessageBag $messageBag)
    {
        parent::doExtraValidation($messageBag);

        if (!$messageBag->isEmpty()) {
            return;
        }

        if (null === $this->getUserKey() && null === $this->getEmail() && null === $this->getPhoneNumber()) {
            $messageBag->add('user_key', __('Must be filled if email and phone fields are missing'));

            return false;
        }

        if (!$this->bonusReceiver() && !$this->isAutoCreateAvailable()) {
            foreach (['user_key', 'email', 'phone'] as $userIdentity) {
                if ($this->has($userIdentity)) {
                    $messageBag->add($userIdentity, __('User was not found'));
                }
            }

            return false;
        }
    }

    public function messages(): array
    {
        return [
            'bonus.required' => __('Fill the field'),
        ];
    }

    public function getUserKey()
    {
        return $this->has('user_key') ? trim($this->get('user_key')) : null;
    }

    public function getPhoneNumber()
    {
        return $this->has('phone') ? \HUser::normalizePhone($this->get('phone'), $this->user->partner->default_country) : null;
    }

    public function getEmail()
    {
        return $this->has('email') ? \HUser::normalizeEmail($this->get('email')) : null;
    }

    public function isAutoCreateAvailable(): bool
    {
        return (bool) $this->get('auto_create');
    }

    public function getUserName()
    {
        return $this->get('name');
    }

    public function bonusReceiver()
    {
        if ($this->user) {
            return $this->user;
        }

        $queryBuilder = null;

        if (!empty($this->getUserKey())) {
            $queryBuilder = User::query()->where('key', $this->getUserKey());
        }

        if (!$queryBuilder && !empty($this->getEmail())) {
            $queryBuilder = User::query()->where('email', $this->getEmail());
        }

        if (!$queryBuilder && !empty($this->getPhoneNumber())) {
            $queryBuilder = User::query()->where('phone', $this->getPhoneNumber());
        }

        if ($queryBuilder) {
            $this->user = $queryBuilder
                ->where('partner_id', $this->user()->partner->id)
                ->first();
        }

        return $this->user;
    }
}
