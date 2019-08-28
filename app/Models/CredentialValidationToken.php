<?php
/**
 * CredentialValidationToken.php
 * Creator: lehadnk
 * Date: 03/09/2018.
 */

namespace App\Models;

use Carbon\Carbon;

/**
 * Class CredentialValidationToken.
 *
 * @property int    $id
 * @property string $token
 * @property bool   $used
 * @property string $phone
 * @property string $email
 * @property int    $person_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class CredentialValidationToken extends AbstractModel
{
    const TOKEN_LIFETIME = 180;

    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = trim(mb_strtolower($value));
    }

    public function person()
    {
        return $this->belongsTo(Person::class, 'person_id');
    }

    public function attemptGeneration(callable $tokenGenerationFunction, int $maxAttempts = 5): bool
    {
        $result = false;

        while ($maxAttempts-- > 0 && !$result) {
            try {
                $this->token = $tokenGenerationFunction();
                $result = $this->save();
            } catch (\Exception $e) {
                // Unique token violation
            }
        }

        return $result;
    }

    public static function getByEmail(string $email): ?CredentialValidationToken
    {
        return self::where('email', '=', $email)->latest()->first();
    }

    public static function getByPhone(string $phone): ?CredentialValidationToken
    {
        return self::where('phone', '=', $phone)->latest()->first();
    }

    public static function validatePhone(string $phone, string $token): ?CredentialValidationToken
    {
        $validationToken = self::getByPhone($phone);

        if ($validationToken && $validationToken->isValid($token)) {
            return $validationToken;
        }

        return null;
    }

    public static function validateEmail(string $email, string $token): ?CredentialValidationToken
    {
        $validationToken = self::getByEmail($email);

        if ($validationToken && $validationToken->isValid($token)) {
            return $validationToken;
        }

        return null;
    }

    public static function validateEmailOrPhone(string $emailOrPhone, string $token): ?CredentialValidationToken
    {
        $validationToken = self::validatePhone($emailOrPhone, $token);

        if ($validationToken) {
            return $validationToken;
        }

        return self::validateEmail($emailOrPhone, $token);
    }

    public function redeem()
    {
        $this->used = true;
        $this->save();
    }

    public function isValid(string $token): bool
    {
        return $this->token === $token && !$this->used && $this->created_at->diffInSeconds(Carbon::now()) <= self::TOKEN_LIFETIME;
    }

    public function isExpired(string $token): bool
    {
        return $this->token === $token && !$this->used && $this->created_at->diffInSeconds(Carbon::now()) > self::TOKEN_LIFETIME;
    }
}
