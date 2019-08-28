<?php

return array_replace_recursive([
    'alarm_to' => '**REMOVED**',
    'send_to_whitelist' => [],
], require __DIR__.'/stages/'.env('APP_ENV').'/sms.php');
