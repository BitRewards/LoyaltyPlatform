<?php
/**
 * Created by PhpStorm.
 * User: nevidimov
 * Date: 23/10/2018
 * Time: 18:13.
 */

namespace App\Console\Commands\Rabbit\Traits;

trait RabbitStarter
{
    private function isRabbitAvailable()
    {
        $connection = @fsockopen(config('rabbit.host'), config('rabbit.port'));

        if (!is_resource($connection)) {
            return false;
        }
        fclose($connection);

        return true;
    }

    protected function sleepAndExitIfRabbitIsUnavailable(): void
    {
        if (!$this->isRabbitAvailable()) {
            sleep(5);

            exit(0);
        }
    }
}
