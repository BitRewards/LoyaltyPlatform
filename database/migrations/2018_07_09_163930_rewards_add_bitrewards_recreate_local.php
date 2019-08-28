<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;

class RewardsAddBitrewardsRecreateLocal extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        if (!App::isLocal()) {
            return;
        }
        $partnerKey = 'test-partner-key';

        $partnerId = DB::table('partners')
            ->select(['id'])
            ->where('key', $partnerKey)
            ->get()
            ->pluck('id')
            ->first();

        if (!$partnerId) {
            return;
        }

        $payoutRewardCount = DB::table('rewards')->where([
            'type' => 'BitrewardsPayout',
            'partner_id' => $partnerId,
        ])->count();

        if (!$payoutRewardCount) {
            DB::table('rewards')->insert(
                [
                    'type' => 'BitrewardsPayout',
                    'price' => null,
                    'value' => null,
                    'title' => null,
                    'partner_id' => $partnerId,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                    'config' => '{"points-to-brw-exchange-rate": 1}',
                    'value_type' => 'fixed',
                    'status' => 'enabled',
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
    }
}
