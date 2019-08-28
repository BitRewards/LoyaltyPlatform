<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersBulkImports extends Migration
{
    public function up()
    {
        DB::beginTransaction();

        \Schema::create('users_bulk_imports', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 255);
            $table->integer('count');
            $table->string('mode', 50);

            $table->integer('partner_id');
            $table->foreign('partner_id')->references('id')->on('partners')->onDelete('cascade');
            $table->index(['partner_id']);

            $table->timestamps();
        });

        DB::commit();
    }

    public function down()
    {
        DB::beginTransaction();

        \Schema::drop('users_bulk_imports');

        DB::commit();
    }
}
