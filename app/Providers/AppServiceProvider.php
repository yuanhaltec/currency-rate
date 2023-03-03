<?php

namespace App\Providers;

use App\Models\Currency;
use App\Models\CurrencyRate;
use App\Repositories\CurrencyConverterRepository;
use App\Repositories\CurrencyConverterRepositoryInterface;
use App\Repositories\CurrencyRateRepository;
use App\Repositories\CurrencyRateRepositoryInterface;
use App\Repositories\CurrencyRepository;
use App\Repositories\CurrencyRepositoryInterface;
use App\Services\CurrencyService;
use App\Services\CurrencyServiceInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(CurrencyRepositoryInterface::class, function ($app) {
            return new CurrencyRepository($app->make(Currency::class));
        });

        $this->app->singleton(CurrencyRateRepositoryInterface::class, function ($app) {
            return new CurrencyRateRepository(
                $app->make(CurrencyRate::class),
                $app->make(CurrencyRepository::class),
                $app->make(CurrencyConverterRepository::class)
            );
        });

        $this->app->singleton(CurrencyServiceInterface::class, function ($app) {
            return new CurrencyService(
                $app->make(CurrencyRepositoryInterface::class),
                $app->make(CurrencyRateRepositoryInterface::class),
            );
        });
        
        $this->app->singleton(CurrencyConverterRepositoryInterface::class, function($app) {
            return new CurrencyConverterRepository;
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
