<?php

namespace App\Http\Requests;

use App\Models\Partner;
use Illuminate\Contracts\Support\MessageBag;

/**
 * @property Partner $partner
 * @property string  $email
 * @property string  $sender_name
 * @property string  $message
 */
class InviteByEmail extends BaseFormRequest
{
    private $emailsArray;

    public function authorize()
    {
        return \Auth::check();
    }

    public function rules()
    {
        return [
            'email' => 'required|max:255',
            'sender_name' => 'max:100',
            'message' => 'max:300',
        ];
    }

    public function doExtraValidation(MessageBag $messageBag)
    {
        if (!$messageBag->isEmpty()) {
            return;
        }
        $this->emailsArray = explode(',', $this->email);

        if (count($this->emailsArray) > 30) {
            $messageBag->add('email', _('This invitation cannot be sent to more than 30 addresses'));

            return;
        }

        foreach ($this->emailsArray as $email) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $messageBag->add('email', _('One of the addresses does not appear to be am email'));
            }
        }
    }

    public function getEmailsArray()
    {
        return $this->emailsArray;
    }
}
