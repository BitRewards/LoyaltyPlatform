<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHelpItemsTable extends Migration
{
    public function up()
    {
        DB::beginTransaction();

        \Schema::create('help_items', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('partner_id')->default(0)->index();
            $table->string('language')->default('')->index();
            $table->text('question')->nullable();
            $table->text('answer')->nullable();
            $table->unsignedInteger('position')->default(100)->index();
            $table->timestamps();
        });

        DB::commit();
    }

    public function down()
    {
        DB::beginTransaction();

        \Schema::drop('help_items');

        DB::commit();
    }
}
