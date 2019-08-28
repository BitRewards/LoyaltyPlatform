<?php

namespace App\Console\Commands\Db;

use App\Models\Credential;
use Illuminate\Console\Command;

class SplitPhoneCredentials extends Command
{
    protected $signature = 'db:split-phone-credentials';

    protected $description = 'Fixes bugged accounts';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Credential::where('type_id', 'phone')
            ->whereNotNull('email')
            ->chunk(500, function ($credentialChunk) {
                foreach ($credentialChunk as $credential) {
                    $this->splitCredential($credential);
                }
            });

        $rows = \DB::select('
            SELECT c.id
            FROM persons p
            LEFT JOIN credentials c ON p.id = c.person_id
            LEFT JOIN users u ON p.id = u.person_id
            GROUP BY c.id
            HAVING count(u.id) = 0;
        ');

        $ids = collect($rows)
            ->pluck('id');

        $wrongCredentials = Credential::whereIn('id', $ids)->get();
        $deletedCredentialsData = [];

        foreach ($wrongCredentials as $wrongCredential) {
            $deletedCredentialsData[] = $wrongCredential->getAttributes();
            $wrongCredential->delete();
        }

        echo json_encode($deletedCredentialsData).PHP_EOL;
    }

    private function splitCredential(Credential $credential)
    {
        $newEmailCredential = new Credential();
        $newEmailCredential->person_id = $credential->person_id;
        $newEmailCredential->partner_group_id = $credential->partner_group_id;
        $newEmailCredential->password = $credential->password;
        $newEmailCredential->is_confirmed = false;
        $newEmailCredential->email = $credential->email;
        $newEmailCredential->type_id = Credential::TYPE_EMAIL;
        $newEmailCredential->save();

        $credential->email = null;
        $credential->save();
    }
}
