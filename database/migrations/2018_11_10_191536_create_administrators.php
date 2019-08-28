<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdministrators extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('administrators', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->text('name')->nullable();
            $table->text('email')->unique();
            $table->text('password');
            $table->text('role');
            $table->text('remember_token')->nullable();
            $table->text('api_token')->nullable();
            $table->integer('partner_id')->nullable();
            $table->foreign('partner_id')->references('id')->on('partners')->onDelete('cascade');

            $table->index('email');
            $table->index('role');
        });

        Schema::table('partners', function (Blueprint $table) {
            $table->dropForeign(['main_user_id']);
        });

        \App\Models\User::where('role', '!=', null)->get()->each(function ($user) {
            $administrator = new \App\Administrator();
            $administrator->name = $user->name;
            $administrator->email = $user->email;
            $administrator->password = $user->password;
            $administrator->remember_token = $user->remember_token;
            $administrator->role = $user->role;
            $administrator->api_token = $user->api_token;
            $administrator->partner_id = $user->partner_id;
            $administrator->save();

            \App\Models\Partner::where('main_user_id', '=', $user->id)
                ->each(function (\App\Models\Partner $partner) use ($administrator) {
                    $partner->main_user_id = $administrator->id;
                    $partner->save();
                });
            $user->delete();
        });

        Schema::table('partners', function (Blueprint $table) {
            $table->foreign(['main_user_id'])->references('id')->on('administrators')->onDelete('cascade');
        });

        Schema::table('users', function (Blueprint $blueprint) {
            $blueprint->dropColumn('role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        DB::transaction(function () {
            Schema::table('partners', function (Blueprint $table) {
                $table->dropForeign(['main_administrator_id']);
                $table->foreign('main_user_id')->references('id')->on('users')->onDelete('cascade');
            });

            Schema::table('users', function (Blueprint $blueprint) {
                $blueprint->string('role')->nullable();
                $blueprint->unique(['email', 'role']);
            });

            Schema::drop('administrators');
        });
    }
}
