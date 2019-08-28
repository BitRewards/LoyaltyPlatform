<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCredentialValidationTokens extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('credential_validation_tokens', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('person_id')->nullable();
            $table->foreign('person_id')->nullable()->references('id')->on('persons')->onDelete('cascade');
            $table->text('token')->unique();
            $table->boolean('used')->default(false);
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('credential_validation_tokens');
    }
}
