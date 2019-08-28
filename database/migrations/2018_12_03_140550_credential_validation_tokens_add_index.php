<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CredentialValidationTokensAddIndex extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        \Schema::table('credential_validation_tokens', function (Blueprint $table) {
            $table->index(['email', 'created_at']);
            $table->index(['phone', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        \Schema::table('credential_validation_tokens', function (Blueprint $table) {
            $table->dropIndex(['email', 'created_at']);
            $table->dropIndex(['phone', 'created_at']);
        });
    }
}
