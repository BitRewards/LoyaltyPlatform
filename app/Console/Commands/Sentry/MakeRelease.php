<?php

namespace App\Console\Commands\Sentry;

use App\Services\SentryService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class MakeRelease extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sentry:release';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends Release notification to Sentry';

    /**
     * Execute the console command.
     */
    public function handle(SentryService $sentry)
    {
        $version = trim(exec('git rev-parse HEAD'));
        $frontendVersion = $version.'-frontend';
        $frontendProject = config('release.projects.loyalty-frontend');
        $backendVersion = $version.'-backend';
        $backendProject = config('release.projects.loyalty-backend');

        $this->comment('Current version: '.$version);
        $this->comment('Frontend version: '.$frontendVersion);
        $this->comment('Backend version: '.$backendVersion);

        if ($sentry->release($frontendProject, $frontendVersion) !== $frontendVersion) {
            $this->error('Unable to create release "'.$frontendVersion.'" for "'.$frontendProject.'" project.');
        } else {
            $sourceMapFile = public_path('build/js/minified/loyalty.min.js.map');
            $sourceMapUrl = url('/build/js/minified/loyalty.min.js.map');

            if (file_exists($sourceMapFile) && is_readable($sourceMapFile) && !$sentry->uploadArtifact($frontendProject, $frontendVersion, $sourceMapFile, $sourceMapUrl)) {
                $this->error('Unable to upload source maps for "'.$frontendProject.'" project');
            }
        }

        if ($sentry->release($backendProject, $backendVersion) !== $backendVersion) {
            $this->error('Unable to create release "'.$backendVersion.'" for "'.$backendProject.'" project.');
        }

        Cache::forever('sentry.release_version', $version);
        Cache::forever('sentry.frontend_release_version', $frontendVersion);
        Cache::forever('sentry.backend_release_version', $backendVersion);

        $this->info('Done.');
    }
}
