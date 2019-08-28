<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;

class AddRefillBitActionForLocal extends Migration
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

        $refillAction = DB::table('actions')->where([
            'type' => 'RefillBit',
            'partner_id' => $partnerId,
        ])->count();

        if (!$refillAction) {
            DB::table('actions')->insert([
                'type' => 'RefillBit',
                'value' => 1,
                'value_type' => 'fixed',
                'title' => 'Refill BIT',
                'partner_id' => $partnerId,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'status' => 'enabled',
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
    }
}
