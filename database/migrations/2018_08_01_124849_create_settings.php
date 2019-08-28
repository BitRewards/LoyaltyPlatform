<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSettings extends Migration
{
    public function up()
    {
        DB::beginTransaction();

        \Schema::create('settings', function (Blueprint $table) {
            $table->string('namespace');
            $table->primary('namespace');
            $table->jsonb('options');
            $table->timestamps();
        });

        DB::commit();
    }

    public function down()
    {
        \Schema::drop('settings');
    }
}
