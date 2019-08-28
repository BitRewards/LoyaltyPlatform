<?php

namespace App\Enums\UsersBulkImport;

class ImportMode
{
    const CREATE_NEW_UPDATE_EXISTING = 'create-new-update-existing';
    const CREATE_NEW_SKIP_EXISTING = 'create-new-skip-existing';
    const SKIP_NEW_UPDATE_EXISTING = 'skip-new-update-existing';

    public static function getLabels()
    {
        return [
            static::CREATE_NEW_UPDATE_EXISTING => 'Автоматически зарегистрировать в программе лояльности новых пользователей, увеличить баланс уже зарегистрированным',
            static::CREATE_NEW_SKIP_EXISTING => 'Автоматически зарегистрировать в программе лояльности новых пользователей, пропустить уже зарегистрированных',
            static::SKIP_NEW_UPDATE_EXISTING => 'Только увеличить баланс зарегистрированным в программе лояльности пользователям, пропустить незарегистрированных',
        ];
    }
}
