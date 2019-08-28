<?php

namespace App\Console\Commands\Partners;

use App\Models\Action;
use App\Models\Partner;
use App\Models\Reward;
use App\Services\PartnerService;
use Illuminate\Console\Command;

class RestoreDefaultBitrewardsEntities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'partners:restore-bitrewards-entities';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restores deleted bitrewards actions and rewards (system only).';

    /**
     * Execute the console command.
     *
     * @param PartnerService $partnerService
     *
     * @return mixed
     */
    public function handle(PartnerService $partnerService)
    {
        $this->_restoreRefillBit($partnerService);
        $this->_restoreExchangeEthToBit($partnerService);
        $this->_restoreBitrewardPayout($partnerService);
    }

    protected function _restoreRefillBit(PartnerService $partnerService)
    {
        $partnerIds = \DB::select(
            'select id from partners where id not in '.
            '(select partner_id from actions where type = \''.Action::TYPE_REFILL_BIT.'\' and is_system = true)'
        );

        if (!count($partnerIds)) {
            $this->info('No partners with deleted refill bit were found.');

            return;
        }

        $partners = Partner::whereIn('id', collect($partnerIds)->pluck('id')->toArray())->get();

        $partners->each(function (Partner $partner) use ($partnerService) {
            if ($partner->isBitrewardsEnabled()) {
                $partnerService->createSystemRefillBitAction($partner);
            }
        });

        $this->info('Refill bit actions were restored for '.count($partners).' partner(s).');
    }

    protected function _restoreExchangeEthToBit(PartnerService $partnerService)
    {
        $partnerIds = \DB::select(
            'select id from partners where id not in '.
            '(select partner_id from actions where type = \''.Action::TYPE_EXCHANGE_ETH_TO_BIT.'\' and is_system = true)'
        );

        if (!count($partnerIds)) {
            $this->info('No partners with deleted exchange eth to bit were found.');

            return;
        }

        $partners = Partner::whereIn('id', collect($partnerIds)->pluck('id')->toArray())->get();

        $partners->each(function (Partner $partner) use ($partnerService) {
            if ($partner->isBitrewardsEnabled()) {
                $partnerService->createSystemExchangeEthToBitAction($partner);
            }
        });

        $this->info('Exchange bit actions were restored for '.count($partners).' partner(s).');
    }

    private function _restoreBitrewardPayout(PartnerService $partnerService)
    {
        $partnerIds = \DB::select(
            'select id from partners where id not in '.
            '(select partner_id from rewards where type = \''.Reward::TYPE_BITREWARDS_PAYOUT.'\')'
        );

        if (!\count($partnerIds)) {
            $this->info('No partners with deleted bitrewards payout were found.');

            return;
        }

        $partners = Partner::whereIn('id', collect($partnerIds)->pluck('id')->toArray())->get();

        $partners->each(function (Partner $partner) use ($partnerService) {
            if ($partner->isBitrewardsEnabled()) {
                $partnerService->createPayoutReward($partner);
            }
        });

        $this->info('Bitrewards payout reward were restored for '.count($partners).' partner(s).');
    }
}
