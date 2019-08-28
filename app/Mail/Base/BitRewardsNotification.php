<?php

namespace App\Mail\Base;

abstract class BitRewardsNotification extends UserNotification
{
    public $isBitRewards = true;

    protected function setupSender()
    {
        $senderName = 'BitRewards';

        $result = $this
            ->from('app@bitrewards.com', $senderName);

        $result->withSwiftMessage(function (\Swift_Message $message) {
            $headers = $message->getHeaders();

            $xSmtpApiHeader = ['filters' => ['dkim' => ['settings' => ['domain' => 'mail.bitrewards.com']]]];

            $headers->addTextHeader('X-SMTPAPI', \HJson::encode($xSmtpApiHeader));
        });

        return $result;
    }
}
