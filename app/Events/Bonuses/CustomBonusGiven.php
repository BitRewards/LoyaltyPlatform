<?php

namespace App\Events\Bonuses;

use App\DTO\CustomBonusData;
use App\Models\Partner;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;

class CustomBonusGiven
{
    use InteractsWithSockets, SerializesModels;

    /**
     * @var Partner
     */
    public $partner;

    /**
     * @var CustomBonusData
     */
    public $bonusData;

    /**
     * Create a new event instance.
     *
     * @param Partner         $partner
     * @param CustomBonusData $bonusData
     */
    public function __construct(Partner $partner, CustomBonusData $bonusData)
    {
        $this->partner = $partner;
        $this->bonusData = $bonusData;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
