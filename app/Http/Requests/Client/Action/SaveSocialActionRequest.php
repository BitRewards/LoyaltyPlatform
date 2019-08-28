<?php

namespace App\Http\Requests\Client\Action;

use App\DTO\StoreEventData;
use App\Http\Requests\BaseFormRequest;
use App\Models\Partner;
use App\Models\StoreEntity;
use App\Models\StoreEvent;
use Illuminate\Support\Str;

/**
 * @property string  $url
 * @property string  $type
 * @property Partner $partner
 * @property string  $image_url
 */
class SaveSocialActionRequest extends BaseFormRequest
{
    public function authorize()
    {
        return !\Auth::guest();
    }

    public function getStoreEventData(): StoreEventData
    {
        return new StoreEventData(
            StoreEvent::ACTION_STORE,
            [
                'url' => $this->url,
                'userCrmKey' => \Auth::user()->key,
                'image_url' => $this->image_url,
                'type' => $this->type,
            ],
            StoreEntity::TYPE_SHARE,
            Str::uuid()->toString()
        );
    }

    public function rules()
    {
        return [
            'type' => 'required|in:instagram,telegram,custom',
            'url' => 'nullable|url',
            'image_url' => 'nullable|url',
        ];
    }
}
