<?php

namespace App\Providers;

use App\Models\Currency;
use App\Models\CurrencyRate;
use App\Repositories\CurrencyRateRepository;
use App\Repositories\CurrencyRepository;
use App\Services\Currency\CurrencyConverterService;
use App\Services\CurrencyService;
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
        $this->app->singleton(CurrencyRepository::class, function ($app) {
            return new CurrencyRepository($app->make(Currency::class));
        });

        $this->app->singleton(CurrencyRateRepository::class, function ($app) {
            return new CurrencyRateRepository(
                $app->make(CurrencyRate::class),
                $app->make(CurrencyRepository::class),
                $app->make(CurrencyConverterService::class)
            );
        });

        $this->app->singleton(CurrencyService::class, function ($app){
            return new CurrencyService(
                $app->make(CurrencyRepository::class),
                $app->make(CurrencyRateRepository::class),
            );
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
