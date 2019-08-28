<?php

namespace App\Console\Commands\Api;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MakeApiEndpoint extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:api-endpoint {name} {--path=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new API endpoint class.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $name = Str::studly($this->argument('name'));

        if (Str::endsWith(Str::lower($name), 'endpoint')) {
            $name = Str::substr($name, 0, Str::length($name) - 8);
        }

        $classPath = app_path('Services/Api/Endpoints/'.$name.'Endpoint.php');

        if (file_exists($classPath)) {
            return $this->error('API Endpoint '.$name.' is already exists.');
        }

        $path = ltrim($this->option('path') ?: $this->ask('Enter endpoint path'), '/');

        $find = ['DummyClass', '{EndpointPath}'];
        $replace = [$name.'Endpoint', $path];

        $classContents = str_replace(
            $find, $replace, file_get_contents(app_path('Console/Commands/Stubs/ApiEndpoint.stub'))
        );

        file_put_contents($classPath, $classContents);

        $this->info('API Endpoint '.$name.' was sucessfully created.');
    }
}
