<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePartnerGroups extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('partner_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });

        Schema::table('partners', function (Blueprint $table) {
            $table->integer('partner_group_id')->nullable();
        });

        \App\Models\Partner::each(function (\App\Models\Partner $partner) {
            $group = new \App\Models\PartnerGroup();
            $group->name = $partner->title;
            $group->save();
            $partner->partner_group_id = $group->id;
            $partner->save();
        });

        Schema::table('partners', function (Blueprint $table) {
            $table->integer('partner_group_id')->nullable(false)->change();
            $table->foreign('partner_group_id')->references('id')->on('partner_groups')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        DB::transaction(function () {
            Schema::table('partners', function (Blueprint $table) {
                $table->dropColumn('partner_group_id');
            });

            Schema::drop('partner_groups');
        });
    }
}
