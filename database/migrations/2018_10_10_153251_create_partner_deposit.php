<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePartnerDeposit extends Migration
{
    public function up(): void
    {
        DB::beginTransaction();

        \Schema::create('partner_deposits', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('partner_id');
            $table->text('status');
            $table->decimal('deposit_amount', 12, 2);
            $table->timestamps();

            $table->foreign('partner_id')->references('id')->on('partners')->onDelete('cascade');
        });

        DB::commit();
    }

    public function down(): void
    {
        DB::beginTransaction();

        \Schema::drop('partner_deposits');

        DB::commit();
    }
}
