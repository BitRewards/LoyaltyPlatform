<?php

namespace App\Console\Commands\Beanstalkd;

use Illuminate\Console\Command;

class ClearQueue extends Command
{
    protected $signature = 'beanstalkd:clear {queue=default}';
    protected $description = 'Clear pending jobs';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $queue = $this->argument('queue');

        $this->info(sprintf('Clearing queue: %s', $queue));

        $pheanstalk = \Queue::getPheanstalk();
        $pheanstalk->useTube($queue);
        $pheanstalk->watch($queue);

        while ($job = $pheanstalk->reserve(0)) {
            $pheanstalk->delete($job);
        }

        $this->info('...cleared.');
    }
}
