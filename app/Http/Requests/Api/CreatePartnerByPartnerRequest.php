<?php

namespace App\Http\Requests\Api;

use App\Administrator;
use App\Http\Requests\BaseFormRequest;
use App\Http\Requests\Traits\PartnerUniqueEmail;
use App\Models\Partner;

/**
 * @property string $title
 * @property string $email
 * @property string $template
 * @property string $options
 */
class CreatePartnerByPartnerRequest extends BaseFormRequest
{
    use PartnerUniqueEmail;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (!$this->user() instanceof Administrator) {
            return false;
        }

        /**
         * @var Administrator
         */
        $user = $this->user();

        if (Partner::PARTNER_GROUP_ROLE_ADMIN === !$user->partner->partner_group_role) {
            return false;
        }

        return $this->user()->can('partner');
    }

    public function rules()
    {
        return [
            'title' => 'required|min:2|max:255',
            'email' => 'required|max:255|email',
            'template' => 'string',
            'options' => 'string',
        ];
    }
}
