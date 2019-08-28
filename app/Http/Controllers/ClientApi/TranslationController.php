<?php

namespace App\Http\Controllers\ClientApi;

use App\Http\Controllers\ClientApiController;
use App\Localization\LocaleStorage;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Artisan;

class TranslationController extends ClientApiController
{
    /**
     * @var LocaleStorage
     */
    protected $localeStorage;

    /**
     * @var Artisan
     */
    protected $artisan;

    public function __construct(LocaleStorage $localeStorage, Artisan $artisan)
    {
        $this->localeStorage = $localeStorage;
        $this->artisan = $artisan;
    }

    public function reload()
    {
        $this->artisan::call('localization:updateFromXsl');

        return $this->responseOk();
    }

    public function localesTranslations(): JsonResponse
    {
        $locales = $this->localeStorage->all();

        if (empty($locales)) {
            $this->artisan::call('localization:updateFromXsl');
        }

        return $this->responseJson($locales);
    }

    public function localeTranslations(string $locale): JsonResponse
    {
        if (!$this->localeStorage->isExist($locale)) {
            return $this->notFound("Locale '{$locale}' not found");
        }

        return $this->responseJson($this->localeStorage->get($locale));
    }
}
