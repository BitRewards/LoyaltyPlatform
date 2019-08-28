<?php

namespace App\Http\Requests\Admin;

class WalletTransactionsRequest extends WalletRequest
{
    public function authorize()
    {
        return parent::authorize() && \Auth::user()->partner->hasTreasuryWallet();
    }
}
