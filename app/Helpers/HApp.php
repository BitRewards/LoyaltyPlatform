<?php

class HApp
{
    const HEADER_AUTH_TOKEN = 'X-Auth-Token';

    const PARAM_AUTOLOGIN_TOKEN = '__alt';

    public static function isPhpUnitRunning()
    {
        return defined('PHP_SAPI') && PHP_SAPI == 'cli' && false !== strpos($_SERVER['argv'][0] ?? null, 'phpunit');
    }

    public static function isTestEnvironment(): bool
    {
        return 'testing' === getenv('APP_ENV');
    }

    public static function isStage($name): bool
    {
        if (is_file(base_path('.stage'))) {
            $stage = trim((string) file_get_contents(base_path('.stage')));

            return $name === $stage;
        }

        return false;
    }

    public static function isProduction()
    {
        return 'production' == \App::environment();
    }

    public static function getCurrentLoggedPartnerTitle(): ?string
    {
        $user = Auth::getUser();

        if ($user instanceof \App\Administrator && $user->isPartner()) {
            return $user->partner->title ?? null;
        }

        return null;
    }
}
