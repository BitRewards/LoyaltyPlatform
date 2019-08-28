<?php

namespace App\Services;

use App\Models\CredentialValidationToken;
use App\Models\Person;
use App\Models\SentSms;
use App\Models\Token;
use App\Jobs\SendSms;

class SmsService
{
    protected function _sendSmsc($phone, $text)
    {
        $url = config('smsc.url');
        $params = array_merge([
            'phones' => $phone,
            'mes' => $text,
            'fmt' => 3,
            'sender' => 'Giftd.ru',
            'charset' => 'utf-8',
        ], config('smsc'));

        $url = $url.'?'.http_build_query($params);

        $result = @file_get_contents($url);

        if (!$result) {
            return false;
        }
        $result = \HJson::decode($result);

        $success = !(iso($result['error']));

        if (!$success) {
            \Log::error('SmsC.ru sending error! ', compact('result', 'param'));

            return false;
        }

        return true;
    }

    protected function _sendNexmo($phone, $text)
    {
        $url = config('nexmo.url');

        $params = [
            'api_key' => config('nexmo.api_key'),
            'api_secret' => config('nexmo.api_secret'),
            'to' => $phone,
            'from' => config('nexmo.from'),
            'text' => $text,
        ];
        $url = $url.'?'.http_build_query($params);

        $result = @file_get_contents($url);

        if (!$result) {
            return false;
        }

        $result = \HJson::decode($result);

        if (!isset($result['messages'])) {
            return false;
        }

        foreach ($result['messages'] as $message) {
            if (0 != $message['status']) {
                \Log::error('Nexmo sending error: '."Error {$message['status']} {$message['error-text']}");

                return false;
            }
        }

        return true;
    }

    public function alarm($text, $immediately = false)
    {
        $phone = config('sms.alarm_to');
        $this->send($phone, $text, $immediately);
    }

    private function isWhitelisted(string $phone): bool
    {
        if (\HApp::isProduction()) {
            return true;
        }

        $whitelist = config('sms.send_to_whitelist');

        if (!is_array($whitelist)) {
            return true;
        }

        return in_array($phone, $whitelist);
    }

    public function send($phone, $text = '', $immediately = false)
    {
        if (!$this->isWhitelisted($phone)) {
            return false;
        }

        if ($immediately) {
            if (null === $text || strlen($phone) < 9) {
                \Log::error('SmsService::send validation error: '.sprintf('phone=%s; text=%s; immediately=%d', $phone, $text, intval($immediately)));

                return false;
            }

            $text = html_entity_decode($text);

            $phone = $this->normalizePhone($phone);

            $sentSms = new SentSms();
            $sentSms->phone = $phone;
            $sentSms->text = $text;
            $sentSms->save();

            if ($this->isRussianPhone($phone)) {
                $result = $this->_sendSmsToRussianPhone($phone, $text);
            } else {
                $result = $this->_sendSmsToInternationalPhone($phone, $text);
            }
        } else {
            $result = 'dispatched';
            dispatch(new SendSms($phone, $text));
        }

        return $result;
    }

    public function confirmPhone($phone)
    {
        $token = Token::add(null, Token::TYPE_CONFIRM_PHONE, $phone, Token::DESTINATION_TYPE_PHONE);

        $text = __('Your confirmation code for the rewards program: %s', $token);

        $this->send($phone, $text, true);
    }

    public function confirmGuestPhone(string $phone)
    {
        $token = new CredentialValidationToken();
        $token->phone = $phone;
        $token->attemptGeneration(function () {
            return random_digit_code(6);
        });

        $text = __('Your confirmation code for the rewards program: %s', $token->token);

        $this->send($phone, $text, true);
    }

    public function confirmPersonPhone(Person $person, string $phone)
    {
        $token = new CredentialValidationToken();
        $token->person_id = $person->id;
        $token->phone = $phone;
        $token->attemptGeneration(function () {
            return random_digit_code(6);
        });

        $text = __('Your confirmation code for the rewards program: %s', $token->token);

        $this->send($phone, $text, true);
    }

    public function confirmPhoneFinish($phone, $token)
    {
        $token = Token::check($token, Token::TYPE_CONFIRM_PHONE, Token::DESTINATION_TYPE_PHONE);

        if ($token) {
            if ($token->destination == $phone) {
                return true;
            }
        }

        return false;
    }

    public function normalizePhone($phone)
    {
        return preg_replace('/[^0-9]+/', '', $phone);
    }

    public function isRussianPhone($phone)
    {
        return preg_match("/[78]9\d{9}/", preg_replace('/[^0-9]+/', '', $phone));
    }

    protected function _sendSmsToRussianPhone($phone, $text)
    {
        return $this->_sendSmsc($phone, $text);
    }

    protected function _sendSmsToInternationalPhone($phone, $text)
    {
        return $this->_sendNexmo($phone, $text);
    }
}
