<?php

namespace App\Console\Commands\Localization;

use App\Localization\CSVReader;
use App\Localization\LocaleStorage;
use Illuminate\Console\Command;

class UpdateFromCsv extends Command
{
    /**
     * @var string
     */
    protected $signature = 'localization:updateFromXsl {--csv-file=}';

    /**
     * @var string
     */
    protected $description = 'Update localization from csv file';

    /**
     * @var CSVReader
     */
    protected $csvReader;

    /**
     * @var LocaleStorage
     */
    protected $localeStorage;

    public function __construct(CSVReader $csvReader, LocaleStorage $localeStorage)
    {
        parent::__construct();

        $this->csvReader = $csvReader;
        $this->localeStorage = $localeStorage;
    }

    public function handle()
    {
        $languageFile = $this->option('csv-file');
        $locales = $this->csvReader->getLocales($languageFile);

        foreach ($locales as $locale) {
            $this->localeStorage->set($locale);
            $this->info("Written {$locale->count()} translates to {$locale->getName()} locale");
        }
    }
}
