<?php
/**
 * CredentialType.php
 * Creator: lehadnk
 * Date: 21/08/2018.
 */

namespace App\Models;

/**
 * Class CredentialType.
 *
 * @property $name
 */
class CredentialType extends AbstractModel
{
    private static $fields = [
        Credential::TYPE_EMAIL => 'email',
        Credential::TYPE_PHONE => 'phone',
        Credential::TYPE_FACEBOOK => 'fb_id',
        Credential::TYPE_VK => 'vk_id',
        Credential::TYPE_TWITTER => 'twitter_id',
        Credential::TYPE_GOOGLE => 'google_id',
    ];

    public static function getField(string $credentialType): string
    {
        if (!array_key_exists($credentialType, self::$fields)) {
            throw new \Exception('No credential type with id '.$credentialType.' found!');
        }

        return self::$fields[$credentialType];
    }
}
