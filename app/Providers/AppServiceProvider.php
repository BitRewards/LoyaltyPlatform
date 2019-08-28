<?php

namespace App\Providers;

use Admitad\Api\Api;
use App\Db\Factory\Registry;
use App\Generators\AffiliateUrlGenerator;
use App\Models\PersonInterface;
use App\Rabbit\RpcErrorHandler;
use App\Services\CustomizationsService;
use App\Services\Fiat\Tickers\Currency\ApiLayerService;
use App\Services\Fiat\Tickers\Currency\RatesApiService;
use App\Services\Fiat\Tickers\CurrencyRatesService;
use App\Services\ImageStorageService\IImageStorage;
use App\Services\ImageStorageService\SelectelImageStorage;
use App\Services\SentryService;
use App\Services\Settings\AdmitadSettings;
use GL\Rabbit\ApplicationProxyInterface;
use GL\Rabbit\ErrorHandlerInterface;
use GL\Rabbit\RpcErrorHandlerInterface;
use GuzzleHttp\Client;
use Illuminate\Container\Container;
use Illuminate\Log\LogManager;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Symfony\Component\Lock\Factory;
use Symfony\Component\Lock\Store\RedisStore;
use Symfony\Component\Lock\Store\RetryTillSaveStore;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @param UrlGenerator $urlGenerator
     */
    public function boot(UrlGenerator $urlGenerator)
    {
        \Validator::extend('amount', function ($attribute, $value, $parameters, $validator) {
            return preg_match('/^\d+(\.\d{2})?$/', $value);
        });
        \Validator::extend('phone', function ($attribute, $value, $parameters, $validator) {
            $onlyDigits = preg_replace('/[^0-9]/', '', $value);

            return strlen($onlyDigits) >= 11;
        });

        $this->app->instance(CustomizationsService::class, app(CustomizationsService::class));

        $this->registerViewComposers();

        \Queue::looping(function () {
            while (\DB::transactionLevel() > 0) {
                \DB::rollBack();
            }
        });

        $urlGenerator->forceScheme('https');
        \URL::forceScheme('https');
    }

    /**
     * Register any application services.
     */
    public function register()
    {
        if ('production' !== $this->app->environment()) {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
            $this->app->register(\Iber\Generator\ModelGeneratorProvider::class);
        }

        $this->app->singleton(\App\Services\StoreEntityService::class);
        $this->app->singleton(\App\Services\StoreEventService::class);
        $this->app->singleton(\App\Services\UserService::class);
        $this->app->singleton(\App\Services\OAuthService::class);
        $this->app->singleton(\App\Services\SignatureService::class);
        $this->app->singleton(\App\Services\SmsService::class);
        $this->app->singleton(\App\Services\UsersBulkImportService::class);
        $this->app->bind(IImageStorage::class, SelectelImageStorage::class);

        $this->app->singleton(SentryService::class, function (Container $app) {
            $token = config('release.auth_token');
            $client = new Client([
                'headers' => [
                    'Authorization' => 'Bearer '.$token,
                    'Content-Type' => 'application/json',
                ],
            ]);

            $organization = config('release.organization');

            return new SentryService($client, $organization);
        });

        $this->app->singleton(Api::class, function (Container $container) {
            $settings = $container->make(AdmitadSettings::class);

            try {
                $accessToken = $settings->accessToken;
            } catch (\Exception $e) {
            }

            return new Api($accessToken ?? null);
        });

        $this->app->singleton(\AMQPConnection::class, function (Container $container) {
            return new \AMQPConnection(
                config('rabbit')
            );
        });

        $this->app->singleton(RpcErrorHandlerInterface::class, function (Container $container) {
            return new RpcErrorHandler($container->get(LogManager::class));
        });

        $this->app->singleton(ErrorHandlerInterface::class, function (Container $container) {
            return $container->get(RpcErrorHandlerInterface::class);
        });

        $this->app->singleton(ApplicationProxyInterface::class, function (Container $container) {
            return new \App\Rabbit\ApplicationProxy();
        });

        $this->app->singleton(AffiliateUrlGenerator::class, function (Container $container) {
            $generator = new AffiliateUrlGenerator();
            $generator->addProvider(new AffiliateUrlGenerator\Provider\AdmitadProvider());

            return $generator;
        });

        $this->app->bind(PersonInterface::class, function (Container $container) {
            return \Auth::user();
        });

        $this->app->singleton(\Predis\Client::class);

        $this->app->singleton(RedisStore::class, function (Container $container) {
            $client = $container->get(\Predis\Client::class);

            return new RedisStore($client);
        });

        $this->app->singleton(Factory::class, function (Container $container) {
            $store = $container->get(RedisStore::class);
            $store = new RetryTillSaveStore($store, 100, 600);

            return new Factory($store);
        });

        $this->app->bind('giftd.api.log', function () {
            //@todo Move it to log channel after upgrate laravel to 5.7
            $handler = new RotatingFileHandler(storage_path('/logs/giftd_api.log'), 7, Logger::DEBUG, true, 0666);
            $logger = new Logger('giftd.api');
            $logger->pushHandler($handler);

            return $logger;
        });

        $this->app->bind('user-store.api.log', function () {
            $formatter = new LineFormatter("[%datetime%]: %message% %context% %extra%\n", 'Y-m-d H:i:s.u');

            $handler = new RotatingFileHandler(storage_path('/logs/user_store.log'), 7, Logger::DEBUG, true, 0666);
            $handler->setFormatter($formatter);

            $logger = new Logger('giftd.api');
            $logger->pushHandler($handler);

            return $logger;
        });

        $this->app->singleton(Registry::class);
        $this->app->bind('registry', Registry::class);

        $this->app->singleton(CurrencyRatesService::class, function (Container $container) {
            $currencyRateService = new CurrencyRatesService();
            $currencyRateService
                ->addTickerService($container->get(RatesApiService::class), 1)
                ->addTickerService($container->get(ApiLayerService::class), 0);

            return $currencyRateService;
        });
    }

    protected function registerViewComposers()
    {
        View::share('sentryRelease', SentryService::currentVersion());
        View::share('sentryFrontendRelease', SentryService::currentVersion('frontend'));
        View::share('sentryBackendRelease', SentryService::currentVersion('backend'));
    }
}
