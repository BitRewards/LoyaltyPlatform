<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\Giftd\ApiClient;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class PushToExternalEmailService implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $userId;

    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    public function handle()
    {
        $user = User::whereId($this->userId)->first();

        if (!$user || !$user->email) {
            return;
        }

        $apiClient = ApiClient::create($user->partner);

        $apiClient->queryCrm('user/pushEmailToExternalEmailServices', [
            'email' => $user->email,
        ]);
    }
}
