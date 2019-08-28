<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersBulkImportsRows extends Migration
{
    public function up()
    {
        DB::beginTransaction();

        \Schema::create('users_bulk_imports_rows', function (Blueprint $table) {
            $table->increments('id');
            $table->text('data');
            $table->boolean('is_skipped')->default(false);
            $table->boolean('is_new_user')->default(false);
            $table->boolean('is_existing_user')->default(false);
            $table->timestampTz('processed_at')->nullable();

            $table->integer('users_bulk_import_id');
            $table->foreign('users_bulk_import_id')->references('id')->on('users_bulk_imports')->onDelete('cascade');
            $table->index(['users_bulk_import_id']);

            $table->timestamps();
        });

        DB::commit();
    }

    public function down()
    {
        DB::beginTransaction();

        \Schema::drop('users_bulk_imports_rows');

        DB::commit();
    }
}
