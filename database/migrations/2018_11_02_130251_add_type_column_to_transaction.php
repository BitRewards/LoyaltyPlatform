<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTypeColumnToTransaction extends Migration
{
    public function up()
    {
        DB::beginTransaction();

        \Schema::table('transactions', function (Blueprint $table) {
            $table->string('type')->nullable();
        });

        $query = <<<SQL
UPDATE 
  transactions 
SET 
  type = (
    CASE 
      WHEN reward_id notnull 
        THEN 'reward' 
        
      WHEN action_id notnull 
        THEN 'action' 
    END
  )
SQL;

        DB::update($query);

        \Schema::table('transactions', function (Blueprint $table) {
            $table->string('type')->nullable(false)->change();
        });

        DB::commit();
    }

    public function down()
    {
        DB::beginTransaction();

        \Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('type');
        });

        DB::commit();
    }
}
