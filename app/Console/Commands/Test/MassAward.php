<?php

namespace App\Console\Commands\Test;

use App\Models\Partner;
use App\Services\PartnerService;
use Illuminate\Console\Command;

class MassAward extends Command
{
    protected $signature = 'test:massaward {partnerId} {points=10} {comment=here it is a test comment} {onlyConfirmedEmails=1}';

    public function handle()
    {
        $partner = Partner::whereId($this->argument('partnerId'))->first();
        $points = $this->argument('points');
        $comment = $this->argument('comment');
        $onlyConfirmedEmails = $this->argument('onlyConfirmedEmails');

        $this->info("Mass award for all users of partner: {$partner->title}");
        $this->info("{$points} points will be given to each user ".($onlyConfirmedEmails ? 'with confirmed email' : ''));
        $this->info("Comment: \"{$comment}\"");

        app(PartnerService::class)->giveCustomBonusToAllUsers($partner, $points, $comment, $onlyConfirmedEmails);
    }
}
