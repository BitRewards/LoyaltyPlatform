<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPartnerToSentEmails extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('sent_emails', function (Blueprint $table) {
            $table
                ->integer('partner_id')
                ->nullable(true);

            $table
                ->foreign('partner_id')
                ->references('id')
                ->on('partners')
                ->onDelete('cascade');

            $table->index('partner_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('sent_emails', function (Blueprint $table) {
            $table->dropColumn('partner_id');
        });
    }
}
