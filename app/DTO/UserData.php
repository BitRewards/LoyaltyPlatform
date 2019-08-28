<?php

namespace App\DTO;

class UserData extends DTO
{
    public $email;
    public $phone;
    public $name;
    public $password;
    public $email_confirmed_at;
    public $phone_confirmed_at;
    public $signup_type;
    public $referrer_id;
    public $referrer_key;

    public $utm_source;
    public $utm_content;
    public $utm_medium;
    public $utm_term;
    public $utm_campaign;
}
