<?php

namespace App\Console\Commands\Api;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MakeApiDefinition extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:api-definition {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make a new API Definition class.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $name = Str::studly($this->argument('name'));

        if (Str::endsWith(Str::lower($name), 'definition')) {
            $name = Str::substr($name, 0, Str::length($name) - 10);
        }

        $classPath = app_path('Services/Api/Definitions/'.$name.'Definition.php');

        if (file_exists($classPath)) {
            return $this->error('API Definition '.$name.' is already exists.');
        }

        $find = ['DummyClass', '{DefinitionName}'];
        $replace = [$name.'Definition', $name];

        $classContents = str_replace(
            $find, $replace, file_get_contents(app_path('Console/Commands/Stubs/ApiDefinition.stub'))
        );

        file_put_contents($classPath, $classContents);

        $this->info('API Definition '.$name.' was sucessfully created.');
    }
}
