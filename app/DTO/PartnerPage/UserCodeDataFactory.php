<?php

namespace App\DTO\PartnerPage;

use App\Models\User;

class UserCodeDataFactory
{
    /**
     * @var \HDate
     */
    protected $dateHelper;

    /**
     * @var \HStr
     */
    protected $stringHelper;

    public function __construct(\HDate $dateHelper, \HStr $stringHelper)
    {
        $this->dateHelper = $dateHelper;
        $this->stringHelper = $stringHelper;
    }

    public function factory(User $user): array
    {
        $codes = [];

        foreach ($user->codes as $code) {
            $codeData = new UserCodeData();
            $codeData->acquiredAtStr = $this->dateHelper::dateTimeFull($code->acquired_at);
            $codeData->acquiredAt = $code->acquired_at->timestamp;
            $codeData->loyaltyCard = $this->stringHelper::mask($code->token);

            $codes[] = $codeData;
        }

        return $codes;
    }
}
