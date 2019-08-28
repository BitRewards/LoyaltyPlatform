<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSavedCoupons extends Migration
{
    public function up(): void
    {
        DB::beginTransaction();

        \Schema::create('saved_coupons', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('partner_id');
            $table->integer('user_id');
            $table->text('code');
            $table->text('status');
            $table->decimal('discount_amount', 12, 2)->nullable();
            $table->decimal('discount_percent', 12, 2)->nullable();
            $table->text('discount_description')->nullable();
            $table->decimal('min_amount_total', 12, 2)->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->timestamps();

            $table->foreign('partner_id')->references('id')->on('partners')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['partner_id', 'code']);
        });

        DB::commit();
    }

    public function down(): void
    {
        DB::beginTransaction();

        \Schema::drop('saved_coupons');

        DB::commit();
    }
}
