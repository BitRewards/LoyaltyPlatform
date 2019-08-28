<?php

namespace App\Localization;

use Illuminate\Support\Facades\Redis;

class LocaleStorage
{
    /**
     * @var Redis
     */
    protected $redis;

    /**
     * @var string
     */
    protected $redisKey;

    public function __construct(Redis $redis, $redisKey = 'localization')
    {
        $this->redis = $redis;
        $this->redisKey = $redisKey;
    }

    public function set(Locale $locale)
    {
        $this->redis::hSet($this->redisKey, $locale->getName(), json_encode($locale));
    }

    public function isExist(string $locale): bool
    {
        return $this->redis::hExists($this->redisKey, $locale);
    }

    public function get(string $locale): array
    {
        $json = $this->redis::hGet($this->redisKey, $locale);
        $result = json_decode($json, true);

        return \is_array($result) ? $result : [];
    }

    public function all(): array
    {
        $locales = $this->redis::hGetAll($this->redisKey);

        return array_map(function ($json) {
            $data = json_decode($json, true);

            return \is_array($data) ? $data : [];
        }, $locales);
    }
}
