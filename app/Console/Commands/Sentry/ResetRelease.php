<?php

namespace App\Console\Commands\Sentry;

use App\Services\SentryService;
use Illuminate\Console\Command;

class ResetRelease extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sentry:reset-release {project} {version}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Resets given release.';

    /**
     * Execute the console command.
     */
    public function handle(SentryService $sentry)
    {
        $project = $this->argument('project');
        $version = $this->argument('version');

        if (!$sentry->clearRelease($project, $version)) {
            $this->error('Unable to clear release '.$version.' from "'.$project.'" project');
        } else {
            $this->info('Release '.$version.' was cleared from "'.$project.'" project');
        }
    }
}
