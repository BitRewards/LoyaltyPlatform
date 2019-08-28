<?php

namespace App\Console\Commands\Partners;

use App\Models\Partner;
use App\Services\HelpService;
use Illuminate\Console\Command;

class CreateDefaultQuestionsForExisted extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'partners:make-questions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates default questions for existed partners (and without questions).';

    /**
     * Execute the console command.
     *
     * @param HelpService $helpService
     */
    public function handle(HelpService $helpService)
    {
        $partners = Partner::whereDoesntHave('helpItems')->get();

        if (!count($partners)) {
            $this->info('No partners available.');

            return;
        }

        $partners->each(function (Partner $partner) use ($helpService) {
            $helpService->createDefaultQuestions($partner);
        });

        $this->info('Questions where created for '.count($partners).' partner(s).');
    }
}
