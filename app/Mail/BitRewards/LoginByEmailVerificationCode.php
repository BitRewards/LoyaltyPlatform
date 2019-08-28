<?php

namespace App\Mail\BitRewards;

use App\Mail\Base\BitRewardsNotification;
use App\Models\User;

class LoginByEmailVerificationCode extends BitRewardsNotification
{
    private $token;

    public function __construct(User $user, string $token)
    {
        parent::__construct($user);

        if (0 == strlen($token) % 3) {
            $result = [];

            for ($i = 0; $i < strlen($token); $i += 3) {
                $result[] = substr($token, $i, 3);
            }
            $this->token = implode(' ', $result);
        } else {
            $this->token = $token;
        }
    }

    protected function getTemplateName(): string
    {
        return 'emails.login-by-email-verification-code';
    }

    protected function getTemplateVariables(): array
    {
        return ['token' => $this->token];
    }

    protected function getSubject(): string
    {
        return __('Your code: %s', $this->token);
    }
}
