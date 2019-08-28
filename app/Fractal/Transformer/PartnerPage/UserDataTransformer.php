<?php

namespace App\Fractal\Transformer\PartnerPage;

use App\DTO\PartnerPage\UserData;
use League\Fractal\TransformerAbstract;

class UserDataTransformer extends TransformerAbstract
{
    /**
     * @var UserCodeDataTransformer
     */
    protected $userCodeDataTransformer;

    public function __construct(UserCodeDataTransformer $userCodeDataTransformer)
    {
        $this->userCodeDataTransformer = $userCodeDataTransformer;
    }

    public function transform(UserData $userData): array
    {
        return [
            'name' => $userData->name,
            'email' => $userData->email,
            'phone' => $userData->phone,
            'avatar' => $userData->avatar,
            'referralLink' => $userData->referralLink,
            'bitTokenSenderAddress' => $userData->bitTokenSenderAddress,
            'balanceAmount' => $userData->balanceAmount,
            'currency' => $userData->currency,
            'codes' => array_map([$this->userCodeDataTransformer, 'transform'], $userData->codes),
        ];
    }
}
