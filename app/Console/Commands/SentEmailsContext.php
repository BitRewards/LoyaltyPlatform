<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SentEmailsContext extends Command
{
    protected $signature = 'sent-emails:update-partner';

    protected $description = 'Update partner id for sent emails';

    public function handle(): void
    {
        $bunchSize = 1000;
        $sql = <<<SQL
WITH mailed_users AS (
    WITH emailed_users as (
        SELECT
            DISTINCT email
        FROM sent_emails
        WHERE partner_id IS NULL
        GROUP BY email
    )
    SELECT
        u.email, count(u.partner_id) as cnt
    FROM users u
             INNER JOIN emailed_users eu ON eu.email = u.email
    GROUP BY u.email
    HAVING count(u.partner_id) = 1
)
SELECT
       u.email, u.partner_id
FROM users u
INNER JOIN mailed_users mu ON mu.email = u.email
LIMIT $bunchSize
SQL;

        while (true) {
            $rows = \DB::select(\DB::raw($sql));

            foreach ($rows as $row) {
                \DB::update('UPDATE sent_emails SET partner_id = ? WHERE email = ?', [
                    $row->partner_id,
                    $row->email,
                ]);
            }

            if (count($rows) < $bunchSize) {
                break;
            }
        }
    }
}
