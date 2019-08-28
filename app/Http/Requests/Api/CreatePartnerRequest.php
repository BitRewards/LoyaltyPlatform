<?php

namespace App\Http\Requests\Api;

use App\Administrator;
use App\Http\Requests\BaseFormRequest;
use App\Models\Partner;
use Illuminate\Contracts\Support\MessageBag;

/**
 * @property string $title
 * @property string $email
 * @property string $giftd_id
 * @property string $giftd_user_id
 * @property string $giftd_api_key
 * @property string $currency
 * @property string $url
 * @property string $code
 * @property string $default_language
 * @property string $customizations
 * @property string $password
 * @property int    $partner_group_id
 */
class CreatePartnerRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('admin');
    }

    public function rules()
    {
        return [
            'title' => 'required|min:2|max:255',
            'email' => 'required|max:255|email',
            'giftd_id' => 'int|required',
            'giftd_user_id' => 'int|required',
            'giftd_api_key' => 'required|max:32',
            'currency' => 'int|required',
            'url' => 'url|max:255',
            'code' => 'max:255',
            'default_language' => 'max:8',
            'customizations' => 'array',
            'password' => 'max:255',
            'partner_group_id' => 'int',
        ];
    }

    public function doExtraValidation(MessageBag $messageBag)
    {
        $existingPartner = Partner::where('giftd_id', $this->giftd_id)->first();

        if (!$existingPartner) {
            $existingUsersCount = Administrator::where([
                ['email', '=', $this->email],
                ['role', '=', Administrator::ROLE_PARTNER],
            ])->count();

            if ($existingUsersCount > 0) {
                $messageBag->add('email', _('This email has already been registered as a partner account'));
            }
        }
    }
}
