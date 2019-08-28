<?php

namespace App\DTO;

class TreasuryWithdrawalData extends DTO
{
    public $id;
    public $status;
    public $amount;
    public $dest_address;
    public $raw_data;
}
