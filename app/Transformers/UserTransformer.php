<?php

namespace App\Transformers;

use App\Models\User;
use App\Services\UserService;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    /**
     * @var array
     */
    protected $requestData = [];
    protected $autoLoginToken;

    /**
     * Transform User model.
     *
     * @param \App\Models\User $user
     *
     * @return array
     */
    public function transform(User $user)
    {
        $item = [
            'email' => $user->email,
            'key' => $user->key,
            'name' => $user->name,
            'picture' => $user->picture,
            'phone' => $user->phone,
            'balance' => $user->balance,
            'codes' => $user->codes->pluck('token'),
            'tracking' => app(UserService::class)->getOrderCommentTracking($user), //TODO: rename to 'order_comment_tracking_code'
            'title' => $user->getTitle(),
            'referralUrl' => $user->referral_link,
            'referrerUserKey' => $user->referrer ? $user->referrer->key : null,
        ];

        if (!empty($this->requestData)) {
            $item['request_data'] = $this->requestData;
        }

        if (!empty($this->autoLoginToken)) {
            $item['autoLoginToken'] = $this->autoLoginToken;
        }

        return $item;
    }

    /**
     * @param array $data
     *
     * @return $this
     */
    public function setRequestData(array $data)
    {
        $this->requestData = $data;

        return $this;
    }

    public function setAutoLoginToken($token)
    {
        $this->autoLoginToken = $token;

        return $this;
    }
}
