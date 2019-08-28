<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePersons extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        DB::beginTransaction();

        $this->createTables();
        $this->migrateUsers();

        Schema::table('users', function (Blueprint $table) {
            $table->integer('person_id')->nullable(false)->change();
        });

        DB::commit();
    }

    private function createTables()
    {
        Schema::create('persons', function (Blueprint $blueprint) {
            $blueprint->increments('id');
            $blueprint->integer('partner_group_id');
            $blueprint->timestamps();
            $blueprint->string('remember_token')->nullable();
        });

        Schema::create('credentials', function (Blueprint $blueprint) {
            $blueprint->increments('id');
            $blueprint->timestamps();
            $blueprint->integer('person_id')->nullable();
            $blueprint->string('email')->nullable();
            $blueprint->dateTime('email_confirmed_at')->nullable();
            $blueprint->string('password')->nullable();
            $blueprint->string('phone')->nullable();
            $blueprint->dateTime('phone_confirmed_at')->nullable();
            $blueprint->string('vk_id')->nullable();
            $blueprint->string('fb_id')->nullable();
            $blueprint->string('twitter_id')->nullable();
            $blueprint->string('google_id')->nullable();
            $blueprint->string('vk_token')->nullable();
            $blueprint->string('fb_token')->nullable();
            $blueprint->boolean('is_confirmed')->nullable();
            $blueprint->integer('partner_group_id');

            $blueprint->index('email');
            $blueprint->index('phone');
            $blueprint->index('vk_id');
            $blueprint->index('fb_id');
            $blueprint->index('google_id');
            $blueprint->index('twitter_id');

            $blueprint->foreign('person_id')->references('id')->on('persons')->onDelete('cascade');
        });

        Schema::table('users', function (Blueprint $blueprint) {
            $blueprint->integer('person_id')->nullable();
            $blueprint->foreign('person_id')->references('id')->on('persons')->onDelete('cascade');
        });
    }

    private function migrateUsers()
    {
        ini_set('memory_limit', '1024M');

        DB::delete('DELETE FROM users WHERE partner_id IS NULL');

        $batchNumber = 1;
        $totalUsers = \App\Models\User::count();

        \App\Models\User::chunk(1000, function ($users) use (&$batchNumber, $totalUsers) {
            $userNumber = $batchNumber * 1000;
            echo "Working on users $userNumber / $totalUsers...\n";
            ++$batchNumber;

            $credentialsToInsert = [];

            $usersToUpdateSql = [];

            for ($i = 0; $i < count($users); ++$i) {
                /**
                 * @var App\Models\User
                 */
                $user = $users[$i];

                $person = new \App\Models\Person();
                $person->partner_group_id = $user->partner->partnerGroup->id;
                $person->save();

                $usersToUpdateSql[] =
                    "UPDATE users SET person_id = {$person->id} WHERE id = {$user->id}";

                $credentialsToInsert[] = $credentials = [
                    'person_id' => $person->id,
                    'email' => $user->email,
                    'email_confirmed_at' => $user->email_confirmed_at,
                    'password' => $user->password,
                    'phone' => $user->phone,
                    'phone_confirmed_at' => $user->phone_confirmed_at,
                    'vk_id' => $user->vk_id,
                    'fb_id' => $user->fb_id,
                    'twitter_id' => $user->twitter_id,
                    'google_id' => $user->google_id,
                    'vk_token' => $user->vk_token,
                    'fb_token' => $user->fb_token,
                    'partner_group_id' => $user->partner->partnerGroup->id,
                ];

                \DB::table('credentials')->insert($credentials);
            }

            echo "Updated person_id for users...\n";

            DB::unprepared(implode('; ', $usersToUpdateSql));

            echo "Inserted credentials to the new table...\n";
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        DB::transaction(function () {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('person_id');
            });
            Schema::drop('credentials');
            Schema::drop('persons');
        });
    }
}
