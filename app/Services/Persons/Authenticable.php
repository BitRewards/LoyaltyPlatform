<?php
/**
 * Created by PhpStorm.
 * User: nevidimov
 * Date: 2018-12-14
 * Time: 18:34.
 */

namespace App\Services\Persons;

interface Authenticatable extends \Illuminate\Contracts\Auth\Authenticatable
{
    const TYPE_USER = 'user';
    const TYPE_ADMINISTRATOR = 'administrator';
    const TYPE_PERSON = 'person';

    public function getName(): ?string;

    public function getEmail(): ?string;

    public function getPhone(): ?string;

    public function getPartnerId(): ?int;

    public function getAuthenticatableType(): string;
}
