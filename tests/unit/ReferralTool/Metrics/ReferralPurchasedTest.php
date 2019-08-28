<?php

namespace ReferralTool\Metrics;

use App\Models\Action;
use App\Models\Partner;
use App\Models\StoreEntity;
use App\Models\Transaction;
use Bitrewards\ReferralTool\Metrics\ReferralConfirmedPurchasedAmountTrend;
use Bitrewards\ReferralTool\Metrics\ReferralConfirmedPurchasedCountTrend;
use Carbon\Carbon;
use Traits\NovaRequestTrait;

class ReferralPurchasedTest extends \Codeception\Test\Unit
{
    use NovaRequestTrait;

    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
        $I = $this->tester;

        $partner = $I->amLoggedAsPartner();

        /** @var Action $referralAction */
        $referralAction = $I->have(Action::class, [
            'partner_id' => $partner->id,
        ], Action::TYPE_ORDER_REFERRAL);

        $anotherReferralAction = $I->have(Action::class, [
            'partner_id' => $I->have(Partner::class),
        ], Action::TYPE_ORDER_REFERRAL);

        $this->generateTransaction($referralAction, Transaction::STATUS_CONFIRMED, 1);
        $this->generateTransaction($referralAction, Transaction::STATUS_CONFIRMED, 2);
        $this->generateTransaction($referralAction, Transaction::STATUS_CONFIRMED, 2);
        $this->generateTransaction($anotherReferralAction, Transaction::STATUS_CONFIRMED, 3);
        $this->generateTransaction($referralAction, Transaction::STATUS_PENDING, 3);
        $this->generateTransaction($referralAction, Transaction::STATUS_REJECTED, 3);
    }

    protected function generateTransaction(
        Action $referralAction,
        string $transactionStatus,
        ?int $createdAt = null
    ) {
        $createdAt = $createdAt ? Carbon::now()->subDays($createdAt) : null;

        $this->tester->haveWithStates(Transaction::class, $transactionStatus, [
            'action_id' => $referralAction->id,
            'partner_id' => $referralAction->partner_id,
            'source_store_entity_id' => $this->tester->haveWithStates(StoreEntity::class, StoreEntity::STATUS_CONFIRMED, [
                'partner_id' => $referralAction->partner_id,
                'created_at' => $createdAt,
            ])->id,
        ], Transaction::TYPE_ACTION);
    }

    public function testAmountTrendCalculate(): void
    {
        $metric = new ReferralConfirmedPurchasedAmountTrend();
        $value = $metric->calculate($this->simpleNovaRequest([
            'range' => 3,
        ]));

        $this->assertEquals([200, 200, 100], array_values($value->trend));
    }

    public function testCountTrendCalculate(): void
    {
        $metric = new ReferralConfirmedPurchasedCountTrend();
        $value = $metric->calculate($this->simpleNovaRequest([
            'range' => 3,
        ]));

        $this->assertEquals([2, 2, 1], array_values($value->trend));
    }
}
