<?php

namespace App\Models;

use App\Db\Traits\SaveHooks;
use App\Models\DTO\TokenConfig;

/**
 * Class Token.
 *
 * @property int    $id
 * @property string $token
 * @property string $type
 * @property int    $owner_user_id
 * @property string destination
 * @property string destination_type
 * @property string tag
 * @property array data
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property User           $owner
 */
class Token extends AbstractModel
{
    use SaveHooks;

    const TYPE_CONFIRM_EMAIL = 'confirm-email';
    const TYPE_CONFIRM_PHONE = 'confirm-phone';
    const TYPE_LOGIN_BY_EMAIL = 'login-by-email';
    const TYPE_MERGE_EMAIL = 'merge-email';
    const TYPE_MERGE_PHONE = 'merge-phone';
    const TYPE_AUTO_LOGIN = 'auto-login';
    const TYPE_RESET_PASSWORD = 'reset-password';
    const TYPE_CHECK_CREDENTIAL_EMAIL = 'credential-email';
    const TYPE_CHECK_CREDENTIAL_PHONE = 'credential-phone';

    const DESTINATION_TYPE_EMAIL = 'email';
    const DESTINATION_TYPE_PHONE = 'phone';
    const DESTINATION_TYPE_WEB = 'web';

    public $timestamps = true;

    const MAX_CREATE_ATTEMPTS = 5;

    protected $table = 'tokens';

    protected $fillable = [
        'token',
        'type',
        'owner_user_id',
        'destination',
        'destination_type',
        'tag',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    private static function getTokenConfig($type, $destinationType)
    {
        switch ($type) {
            case static::TYPE_AUTO_LOGIN:
                return TokenConfig::makeLongLivingToken();

            case static::TYPE_MERGE_PHONE:
            case static::TYPE_MERGE_EMAIL:
                return TokenConfig::makeShortLivingNumericToken();

            case static::TYPE_LOGIN_BY_EMAIL:
                return TokenConfig::makeTokenForEmailAuth();

            case static::TYPE_CONFIRM_EMAIL:
                return TokenConfig::makeOneDayToken();

            case static::TYPE_CONFIRM_PHONE:
                return TokenConfig::makeShortLivingNumericToken();

            case static::TYPE_RESET_PASSWORD:
                $result =
                    self::DESTINATION_TYPE_PHONE == $destinationType ?
                        TokenConfig::makeShortLivingNumericToken() :
                        TokenConfig::makeOneDayToken();

                return $result->setExclusive(true);

            default:
                throw new \RuntimeException("Unknown token type passed: $type");
        }
    }

    /**
     * @deprecated use \App\Services\TokenService::create instead
     */
    public static function add($ownerId, $type, $destination = null, $destinationType = self::DESTINATION_TYPE_EMAIL, array $data = null, $tag = null)
    {
        $token = new Token([
            'owner_user_id' => $ownerId,
            'type' => $type,
            'destination' => $destination,
            'destination_type' => $destinationType,
            'data' => $data,
            'tag' => $tag,
        ]);

        if (!$token->generateTokenAndSave()) {
            throw new \Exception('Token creation failed! Last id was: '.$token->id);
        }

        return $token->token;
    }

    public static function check($token, $type, $destinationType = self::DESTINATION_TYPE_EMAIL, $destination = null, $ownerId = null, $deleteOnSuccess = true)
    {
        $attributes = [
            'token' => $token,
            'type' => $type,
        ];

        if (null !== $destinationType) {
            $attributes['destination_type'] = $destinationType;
        }

        if (null !== $destination) {
            $attributes['destination'] = $destination;
        }
        $token = Token::model()->whereAttributes($attributes)->first();

        if ($token) {
            if ($token->isExpired()) {
                $token->delete();

                return null;
            }

            $tokenData = clone $token;

            if (null !== $ownerId && $token->owner_user_id != $ownerId) {
                return null;
            }

            if ($deleteOnSuccess) {
                $token->delete();
            }

            return $tokenData;
        } else {
            return null;
        }
    }

    public function isExpired()
    {
        $config = static::getTokenConfig($this->type, $this->destination_type);

        return $config->lifetime && ($this->created_at->getTimestamp() + $config->lifetime < time());
    }

    public static function removeAll($ownerId, $type, $destinationType = self::DESTINATION_TYPE_EMAIL)
    {
        Token::model()->deleteWhereAttributes([
            'owner_user_id' => $ownerId,
            'type' => $type,
            'destination_type' => $destinationType,
        ]);
    }

    private static function generate(TokenConfig $config)
    {
        switch ($config->format) {
            case TokenConfig::FORMAT_SHORT_NUMERIC:
                return random_digit_code(6);

            case TokenConfig::FORMAT_NUMERIC_8_DIGIT:
                return random_digit_code(8);

            default:
                return mb_strtolower(str_random(16), 'UTF-8');
        }
    }

    public function isOwnerPhoneDestination(): bool
    {
        return self::DESTINATION_TYPE_PHONE === $this->destination_type
            && $this->owner->phone === $this->destination;
    }

    public function isOwnerEmailDestination(): bool
    {
        return self::DESTINATION_TYPE_EMAIL === $this->destination_type
            && $this->owner->email === $this->destination;
    }

    public function generateTokenAndSave(): bool
    {
        $config = self::getTokenConfig($this->type, $this->destination_type);

        if ($config->exclusive) {
            Token::removeAll(
                $this->owner_user_id,
                $this->destination_type,
                $this->destination
            );
        }

        $count = Token::MAX_CREATE_ATTEMPTS;

        while (0 < $count--) {
            try {
                $this->token = Token::generate($config);
                $this->saveOrFail();

                return true;
            } catch (\Exception $e) {
            }
        }

        return false;
    }
}
