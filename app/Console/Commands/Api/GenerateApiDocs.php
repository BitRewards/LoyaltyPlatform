<?php

namespace App\Console\Commands\Api;

use Illuminate\Console\Command;
use App\Services\Api\ApiDocsGenerator;
use App\Providers\ApiDocsServiceProvider;
use App\Services\Api\Specification\ApiSpecification;

class GenerateApiDocs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:api-docs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates public API Documentation';

    public function __construct()
    {
        parent::__construct();

        // Register ApiDocsServiceProvider here,
        // so it won't load on every request.

        app()->register(ApiDocsServiceProvider::class);
    }

    /**
     * Execute the console command.
     *
     * @param ApiDocsGenerator $generator
     *
     * @return mixed
     */
    public function handle(ApiDocsGenerator $generator)
    {
        \HLanguage::setLanguage(\HLanguage::LANGUAGE_RU);
        $specification = $this->getApiSpecification();
        $docsRu = $generator->generate($specification);

        \HLanguage::setLanguage(\HLanguage::LANGUAGE_EN);
        $specification = $this->getApiSpecification();
        $docsEn = $generator->generate($specification);

        $apiDir = public_path('/api');

        if (!is_dir($apiDir)) {
            mkdir($apiDir);
        }

        file_put_contents(public_path('/api/specification.ru.yml'), $docsRu);
        file_put_contents(public_path('/api/specification.en.yml'), $docsEn);

        // Remove previously cached specifications and generate new cache.
        $rebuildCache = true;
        $generator->resetSpecificationCache('ru', $rebuildCache);
        $generator->resetSpecificationCache('en', $rebuildCache);

        $this->info('API Specifications were saved.');
    }

    /**
     * Get the API Specification.
     *
     * @return ApiSpecification
     */
    protected function getApiSpecification()
    {
        return new ApiSpecification([
            'swagger' => '2.0',
            'info' => [
                'title' => __('Loyalty API'),
                'description' => __('BitRewards Loyalty API'),
                'version' => config('api.version'),
            ],
            'host' => config('api.host'),
            'basePath' => '/'.trim(config('api.base_path'), '/'),
            'schemes' => [config('api.scheme')],
            'produces' => ['application/json'],
            'securityDefinitions' => [
                'api_key' => [
                    'type' => 'apiKey',
                    'name' => 'api_token',
                    'in' => 'query',
                ],
            ],
            'security' => [
                [
                    'api_key' => [],
                ],
            ],
        ]);
    }
}
