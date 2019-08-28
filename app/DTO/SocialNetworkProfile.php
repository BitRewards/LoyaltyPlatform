<?php

namespace App\DTO;

class SocialNetworkProfile extends DTO
{
    public function __construct($name = null, $email = null, $picture = null, $socialNetwork = null, $socialNetworkId = null, $partnerKey = null, $phone = null)
    {
        $this->name = $name;
        $this->email = $email;
        $this->picture = $picture;
        $this->socialNetwork = $socialNetwork;
        $this->socialNetworkId = $socialNetworkId;
        $this->partnerKey = $partnerKey;
        $this->phone = $phone;
    }

    public $name;
    public $email;
    public $picture;
    public $socialNetwork;
    public $socialNetworkId;
    public $partnerKey;
    public $phone;
}
