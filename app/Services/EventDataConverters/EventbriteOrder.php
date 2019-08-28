<?php

namespace App\Services\EventDataConverters;

use App\DTO\OrderData;
use App\Exceptions\MalformedRawEventDataException;
use App\Models\Partner;
use App\Models\StoreEvent;
use Carbon\Carbon;

class EventbriteOrder extends Base
{
    public function __construct(StoreEvent $storeEvent)
    {
        parent::__construct($storeEvent);

        if (isset($this->data['body'])) {
            // hotfix sorry — some guzzle stuff
            $this->data = $this->data['body'];
        }
    }

    public function getAmountTotal()
    {
        $orderData = $this->data;

        return $orderData['costs']['gross']['value'] ?? null;
    }

    private function isRefunded(): bool
    {
        return (bool) ($this->data['refund_requests'] ?? null);
    }

    private function isDelivered(): bool
    {
        return !$this->isRefunded() && 'placed' === $this->data['status'];
    }

    private function isPaid(): bool
    {
        return !$this->isRefunded() && 'placed' === $this->data['status'];
    }

    public function getEntityExternalId()
    {
        return $this->data['id'] ?? null;
    }

    public function getConvertedData(): OrderData
    {
        if (!isset($this->data['email'])) {
            throw new MalformedRawEventDataException();
        }

        return OrderData::make([
            'amountTotal' => $this->getAmountTotal(),
            'isPaid' => $this->isPaid(),
            'isDelivered' => $this->isDelivered(),
            'userCrmKey' => null,
            'refUserCrmKey' => null,
            'managerComment' => null,
            'comment' => null,
            'email' => $this->data['email'] ?? null,
            'name' => $this->data['name'] ?? null,
            'phone' => null,
            'promoCodes' => $this->getPromoCodes(),
            'createdAt' => Carbon::parse($this->data['created'] ?? $this->data['changed'] ?? Carbon::now()->__toString())->__toString(),
            'statusAutoFinishesAt' => $this->getStatusAutoFinishTimestamp(),
        ]);
    }

    /**
     * Returns order promo codes list.
     *
     * @return array
     */
    public function getPromoCodes(): array
    {
        $promoCodes = collect([]);
        $attendees = collect(array_get($this->data, 'attendees', []));

        $attendees->each(function ($attendee) use ($promoCodes) {
            $promoCodes->push($this->extractPromoCodeFromAttendee($attendee));
        });

        return $promoCodes->reject(null)->unique()->toArray();
    }

    /**
     * Get promo code from order attendee (if exists).
     *
     * @param array|null $attendee
     *
     * @return string|null
     */
    protected function extractPromoCodeFromAttendee(array $attendee = null)
    {
        if (!\is_array($attendee)) {
            return null;
        }

        return array_get($attendee, 'promotional_code.code');
    }

    private function getStatusAutoFinishTimestamp()
    {
        $eventStarts = Carbon::parse($this->data['event']['start']['utc']);

        if ($eventStarts) {
            $defaultAutoConfirmInterval = -3 * 24 * 3600; // 3 days before event
            $autoConfirmInterval = $this->getStoreEvent()->partner->getSetting(Partner::SETTINGS_EVENTBRITE_AUTO_CONFIRM_TRANSACTIONS_AFTER_EVENT_START_INTERVAL, $defaultAutoConfirmInterval);
            // $autoConfirmInterval is in seconds, can be negative
            $eventStarts->addSeconds($autoConfirmInterval);

            return $eventStarts->__toString();
        }

        return null;
    }

    public function getExternalEventCreatedAt(): ?Carbon
    {
        if (isset($this->data['changed'])) {
            return Carbon::parse($this->data['changed']);
        }

        return null;
    }
}
