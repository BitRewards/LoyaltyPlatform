<?php

namespace App\Http\Controllers;

use App\Models\SentEmail;

class EmailController extends Controller
{
    public function view($token)
    {
        $sentEmail = SentEmail::whereToken($token)->firstOrFail();
        /*
         * @var SentEmail $sentEmail
         */
        return $sentEmail->body;
    }
}
