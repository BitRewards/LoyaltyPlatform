<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\SmsService;

class SendSms implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $phone;
    protected $text;

    public function __construct($phone, $text)
    {
        $this->phone = $phone;
        $this->text = $text;
    }

    public function handle()
    {
        app(SmsService::class)->send($this->phone, $this->text, true);
    }
}
