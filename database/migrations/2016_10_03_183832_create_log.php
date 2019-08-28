<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLog extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        DB::beginTransaction();

        Schema::create('log', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->longText('message')->nullable();
            $table->string('level_name')->nullable();
            $table->jsonb('extra')->nullable();
            $table->timestampsTz();
        });

        DB::commit();
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        \Schema::drop('log');
    }
}
