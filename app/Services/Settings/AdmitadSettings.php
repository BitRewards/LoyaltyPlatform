<?php

namespace App\Services\Settings;

use App\Models\Partner;
use App\Models\Settings;

/**
 * @property string $clientId
 * @property string $clientSecret
 * @property string $accessToken
 * @property string $refreshToken
 * @property string $partnerKey
 */
class AdmitadSettings
{
    /**
     * @var Settings
     */
    protected $settingsModel;

    /**
     * @var Settings
     */
    protected $admitadSettings;

    /**
     * @var Partner
     */
    protected $partnerModel;

    protected $availableOptions = [
        'clientId',
        'clientSecret',
        'accessToken',
        'refreshToken',
        'partnerKey',
    ];

    public function __construct(Settings $settingsModel, Partner $partnerModel)
    {
        $this->settingsModel = $settingsModel;
        $this->partnerModel = $partnerModel;
    }

    public function getAvailableOptions(): array
    {
        return $this->availableOptions;
    }

    protected function getAdmitadSetting(): Settings
    {
        if (null === $this->admitadSettings) {
            $this->admitadSettings = $this->settingsModel->find('admitad');
        }

        if (null === $this->admitadSettings) {
            $admitadSettings = new Settings([
                'namespace' => 'admitad',
                'options' => [],
            ]);

            $admitadSettings->saveOrFail();

            $this->admitadSettings = $admitadSettings;
        }

        return $this->admitadSettings;
    }

    public function getPartner()
    {
        if ($this->partnerKey) {
            return $this->partnerModel->whereAttributes([
                'key' => $this->partnerKey,
            ])->first();
        }

        return null;
    }

    public function update()
    {
        $this->getAdmitadSetting()->saveOrFail();
    }

    public function __set($key, $value)
    {
        if (!\in_array($key, $this->availableOptions, true)) {
            throw new \InvalidArgumentException("Unknown '{$key}' option");
        }

        $this->getAdmitadSetting()->setAttribute("options->{$key}", $value);
    }

    public function __get($key)
    {
        if (!\in_array($key, $this->availableOptions, true)) {
            throw new \InvalidArgumentException("Unknown '{$key}' option");
        }

        return $this->getAdmitadSetting()->options[$key] ?? null;
    }
}
