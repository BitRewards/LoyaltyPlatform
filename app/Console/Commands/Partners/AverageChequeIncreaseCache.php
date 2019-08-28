<?php

namespace App\Console\Commands\Partners;

use App\Models\Partner;
use App\Services\PartnerService;
use Bitrewards\ReferralTool\Traits\DefaultRangesTrait;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class AverageChequeIncreaseCache extends Command
{
    use DefaultRangesTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'partners:average-cheque-cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update cache for average partner cheques';

    public function handle(PartnerService $partnerService)
    {
        Partner::chunkById(100, function (Collection $collection) use ($partnerService) {
            $collection->each(function (Partner $partner) use ($partnerService) {
                $this->line("Processing partner id={$partner->id}");

                foreach ($this->ranges() as $range => $rangeLabel) {
                    try {
                        $partnerService->getAverageChequeIncrease($partner, $range, false);
                    } catch (\Exception $e) {
                        $this->error("Processing partner id={$partner->id} failed");
                    }
                }
            });
        });
    }
}
