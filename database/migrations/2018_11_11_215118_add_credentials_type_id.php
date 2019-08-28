<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCredentialsTypeId extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        echo "Adding type_id to credentials...\n";

        Schema::table(
            'credentials',
            function (Blueprint $table) {
                $table->string('type_id')
                    ->nullable();
            }
        );

        $credentialsToBeDeleted = [];

        echo "Fetching count of credentials...\n";

        $totalCount = \App\Models\Credential::count();
        $currentIndex = 0;

        \App\Models\Credential::chunk(
            5000,
            function ($chunk) use (&$credentialsToBeDeleted, &$currentIndex, $totalCount) {
                foreach ($chunk as $credential) {
                    /*
                     * @var Credential $credential
                     */
                    ++$currentIndex;
                    echo "Processing credential $currentIndex / $totalCount...\n";

                    if ($this->isEmpty($credential)) {
                        echo $credential->id.' is empty, deleting'.PHP_EOL;
                        $credentialsToBeDeleted[] = $credential;
                    } elseif ($this->doesRequireSplit($credential)) {
                        echo $credential->id.' requires split'.PHP_EOL;
                        $this->split($credential);
                        $credentialsToBeDeleted[] = $credential;
                    } else {
                        echo $credential->id.' does not require split'.PHP_EOL;

                        if (!empty($credential->email)) {
                            $credential->type_id = \App\Models\Credential::TYPE_EMAIL;
                            $credential->is_confirmed = $credential->email_confirmed_at ? true : null;
                        }

                        if (!empty($credential->phone)) {
                            $credential->type_id = \App\Models\Credential::TYPE_PHONE;
                            $credential->is_confirmed = $credential->phone_confirmed_at ? true : null;
                        }

                        if (!empty($credential->fb_id)) {
                            $credential->type_id = \App\Models\Credential::TYPE_FACEBOOK;
                            $credential->is_confirmed = true;
                        }

                        if (!empty($credential->vk_id)) {
                            $credential->type_id = \App\Models\Credential::TYPE_VK;
                            $credential->is_confirmed = true;
                        }

                        if (!empty($credential->twitter_id)) {
                            $credential->type_id = \App\Models\Credential::TYPE_TWITTER;
                            $credential->is_confirmed = true;
                        }

                        if (!empty($credential->google_id)) {
                            $credential->type_id = \App\Models\Credential::TYPE_GOOGLE;
                            $credential->is_confirmed = true;
                        }

                        if (null === $credential->type_id) {
                            echo $credential->id.PHP_EOL;
                        }

                        $credential->save();
                    }
                }
            }
        );

        $countDeleted = count($credentialsToBeDeleted);
        echo "Deleting $countDeleted credentials...\n";

        \App\Models\Credential::whereIn('id', array_pluck($credentialsToBeDeleted, 'id'))
            ->delete();

        echo "Deleted credentials!\n";
    }

    private function isEmpty(\App\Models\Credential $credential)
    {
        $fieldsCnt = !empty($credential->email) + !empty($credential->phone) + !empty($credential->fb_id) + !empty($credential->vk_id) + !empty($credential->twitter_id) + !empty($credential->google_id);

        return 0 == $fieldsCnt;
    }

    private function doesRequireSplit(\App\Models\Credential $credential)
    {
        $fieldsCnt = !empty($credential->email) + !empty($credential->phone) + !empty($credential->fb_id) + !empty($credential->vk_id) + !empty($credential->twitter_id) + !empty($credential->google_id);

        return $fieldsCnt > 1;
    }

    private function split(\App\Models\Credential $oldCredential)
    {
        if (!empty($oldCredential->email)) {
            DB::table('credentials')->insert([
                'person_id' => $oldCredential->person_id,
                'email' => $oldCredential->email,
                'email_confirmed_at' => $oldCredential->email_confirmed_at,
                'password' => $oldCredential->password,
                'type_id' => \App\Models\Credential::TYPE_EMAIL,
                'is_confirmed' => $oldCredential->email_confirmed_at ? true : null,
                'partner_group_id' => $oldCredential->partner_group_id,
            ]);
        }

        if (!empty($oldCredential->phone)) {
            DB::table('credentials')->insert([
                'person_id' => $oldCredential->person_id,
                'phone' => $oldCredential->phone,
                'phone_confirmed_at' => $oldCredential->phone_confirmed_at,
                'password' => $oldCredential->password,
                'type_id' => \App\Models\Credential::TYPE_PHONE,
                'is_confirmed' => $oldCredential->phone_confirmed_at ? true : null,
                'partner_group_id' => $oldCredential->partner_group_id,
            ]);
        }

        if (!empty($oldCredential->fb_id)) {
            DB::table('credentials')->insert([
                'person_id' => $oldCredential->person_id,
                'fb_id' => $oldCredential->fb_id,
                'fb_token' => $oldCredential->fb_token,
                'type_id' => \App\Models\Credential::TYPE_FACEBOOK,
                'is_confirmed' => true,
                'partner_group_id' => $oldCredential->partner_group_id,
            ]);
        }

        if (!empty($oldCredential->vk_id)) {
            DB::table('credentials')->insert([
                'person_id' => $oldCredential->person_id,
                'vk_id' => $oldCredential->vk_id,
                'vk_token' => $oldCredential->vk_token,
                'type_id' => \App\Models\Credential::TYPE_VK,
                'is_confirmed' => true,
                'partner_group_id' => $oldCredential->partner_group_id,
            ]);
        }

        if (!empty($oldCredential->twitter_id)) {
            DB::table('credentials')->insert([
                'person_id' => $oldCredential->person_id,
                'twitter_id' => $oldCredential->twitter_id,
                'type_id' => \App\Models\Credential::TYPE_TWITTER,
                'is_confirmed' => true,
                'partner_group_id' => $oldCredential->partner_group_id,
            ]);
        }

        if (!empty($oldCredential->google_id)) {
            DB::table('credentials')->insert([
                'person_id' => $oldCredential->person_id,
                'twitter_id' => $oldCredential->google_id,
                'type_id' => \App\Models\Credential::TYPE_GOOGLE,
                'is_confirmed' => true,
                'partner_group_id' => $oldCredential->partner_group_id,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table(
            'credentials',
            function (Blueprint $table) {
                $table->dropColumn('type_id');
            }
        );
    }
}
