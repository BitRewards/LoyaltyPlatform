<?php

use App\Administrator;
use App\Models\Partner;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MoveMainAdministrator extends Migration
{
    public function up()
    {
        DB::transaction(function () {
            Schema::table('administrators', function (Blueprint $table) {
                $table->addColumn('boolean', 'is_main')->nullable();
                $table->unique(['partner_id', 'is_main']);
            });

            Partner::query()
                ->whereNotNull('main_administrator_id')
                ->chunk(100, function ($partners) {
                    /** @var Partner $partner */
                    foreach ($partners as $partner) {
                        $administrator = Administrator::find($partner->main_administrator_id);
                        $administrator->is_main = true;
                        $administrator->saveOrFail();
                    }
                });

            Schema::table('partners', function (Blueprint $table) {
                $table->dropColumn('main_administrator_id');
            });
        });
    }

    public function down()
    {
        DB::transaction(function () {
            Schema::table('partners', function (Blueprint $table) {
                $table->addColumn('integer', 'main_administrator_id')->nullable();

                $table
                    ->foreign(['main_administrator_id'])
                    ->references('id')
                    ->on('administrators')
                    ->onDelete('cascade');
            });

            Administrator::query()
                ->where('role', Administrator::ROLE_PARTNER)
                ->where('is_main', true)
                ->chunk(100, function ($administrators) {
                    /** @var Administrator $administrator */
                    foreach ($administrators as $administrator) {
                        $administrator->partner->main_administrator_id = $administrator->id;
                        $administrator->partner->saveOrFail();
                    }
                });

            Schema::table('administrators', function (Blueprint $table) {
                $table->dropUnique(['partner_id', 'is_main']);
                $table->dropColumn('is_main');
            });
        });
    }
}
