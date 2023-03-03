<?php

namespace Tests\Feature;

use App\Repositories\CurrencyConverterRepository;
use App\Repositories\CurrencyConverterRepositoryInterface;
use App\Repositories\CurrencyRepositoryInterface;
use Tests\TestCase;
use App\Services\CurrencyServiceInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Validation\ValidationException;
use Mockery\MockInterface;
use Throwable;

class CurrencyServiceTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    const CURRENCY = 'EUR';
    const CURRENCY_2= 'JPY';

    public function test_create()
    {
        $result = false;        
        $this->createCurrency();

        if ($this->findCurrency(self::CURRENCY)) {
            $result = true;
        }
        
        $this->assertTrue($result);
    }

    public function test_update()
    {
        $result = false;  
        $currency = $this->createCurrency();
        $currencyService = $this->app->make(CurrencyServiceInterface::class);
        $request = new Request(['currency' => self::CURRENCY_2]);
        $currencyService->update($request, $currency->id);

        if ($this->findCurrency(self::CURRENCY_2)) {
            $result = true;
        }

        $this->assertTrue($result);
    }

    public function test_validation()
    {
        try {
            $currencyService = $this->app->make(CurrencyServiceInterface::class);
            $request = new Request();
            $currencyService->create($request);  
        } catch (Throwable $e) {
            $this->assertInstanceOf(ValidationException::class, $e);
        }

        try {
            $this->createCurrency();
            $this->createCurrency();
        } catch (Throwable $e) {            
            $this->assertInstanceOf(ValidationException::class, $e);
        }
        
        try {
            $currency = $this->createCurrency();
            $request = new Request();
            $currencyService->update($request, $currency->id);
        } catch (Throwable $e) {            
            $this->assertInstanceOf(ValidationException::class, $e);
        }

        try {
            $currency = $this->createCurrency();
            $request = new Request();
            $currencyService->update($request, $currency->id);
        } catch (Throwable $e) {            
            $this->assertInstanceOf(ValidationException::class, $e);
        }
    }

    protected function createCurrency($currency = null) 
    {
        $currency ??= self::CURRENCY;
        $currencyService = $this->app->make(CurrencyServiceInterface::class);
        $request = new Request(['currency' => $currency]);
        $currency = $currencyService->create($request);  
        return $currency;
    }

    protected function findCurrency($currency)
    {
        $currencyRepository = $this->app->make(CurrencyRepositoryInterface::class);
        return $currencyRepository->findByCurrency($currency);
    }

    public function test_get()
    {
        $currencyService = $this->app->make(CurrencyServiceInterface::class);
        $currencyService->get();        
        $this->assertTrue(true);
    }

    public function test_delete()
    {
        $result = false;
        $this->createCurrency();
        $currency = $this->findCurrency(self::CURRENCY);
        
        if ($currency) {            
            $currencyService = $this->app->make(CurrencyServiceInterface::class);
            $currencyService->delete($currency->id);
            $currency = $this->findCurrency(self::CURRENCY);
            
            if (!$currency) {
                $result = true;
            }
        }

        $this->assertTrue($result);
    }

    public function test_currency_not_found()
    {
        $id = '999999';
        $currencyService = $this->app->make(CurrencyServiceInterface::class);

        try {
            $request = new Request(['currency' => self::CURRENCY_2]);
            $currencyService->update($request, $id);
        } catch (Throwable $e) {            
            $this->assertInstanceOf(ModelNotFoundException::class, $e);
        }

        try {
            $request = new Request(['currency' => self::CURRENCY_2]);
            $currencyService->delete($id);
        } catch (Throwable $e) {            
            $this->assertInstanceOf(ModelNotFoundException::class, $e);
        }
    }

    public function test_rate()
    {
        
        $result = false;
        $this->mock(CurrencyConverterRepository::class, function(MockInterface $mock) {
            $mock->shouldReceive('convert')->once()
                ->with(self::CURRENCY, self::CURRENCY_2, date('Y-m-d'))
                ->andReturn([
                    'date' => date('Y-m-d'),
                    'from' => self::CURRENCY,
                    'to' => self::CURRENCY_2,
                    'rate' => 10
                ]);
        });
        $this->createCurrency();
        $this->createCurrency(self::CURRENCY_2);
        $currencyService = $this->app->make(CurrencyServiceInterface::class);
        $request = new Request(['date' => date('Y-m-d')]);
        $rate = $currencyService->rate($request, self::CURRENCY, self::CURRENCY_2);
        $this->assertEquals($rate['rate'], 10);
        if ($rate) {
            $result = true;
        }

        $this->assertTrue($result);

        $rateExists = $currencyService->rate($request, self::CURRENCY, self::CURRENCY_2);
        $this->assertEquals($rate, $rateExists);
    }

    public function test_unknown_rate_from()
    {
        try {
            $result = false;       
            $this->createCurrency();             
            $currencyService = $this->app->make(CurrencyServiceInterface::class);
            $request = new Request(['date' => date('Y-m-d')]);
            $rate = $currencyService->rate($request, self::CURRENCY, self::CURRENCY_2);

            if ($rate) {
                $result = true;
            }

            $this->assertTrue($result);
        } catch (Throwable $e) {            
            $this->assertInstanceOf(ModelNotFoundException::class, $e);
        }
    }

    public function test_unknown_rate_to()
    {
        try {
            $result = false;        
            $this->createCurrency(self::CURRENCY_2);
            $currencyService = $this->app->make(CurrencyServiceInterface::class);
            $request = new Request(['date' => date('Y-m-d')]);
            $rate = $currencyService->rate($request, self::CURRENCY, self::CURRENCY_2);

            if ($rate) {
                $result = true;
            }

            $this->assertTrue($result);
        } catch (Throwable $e) {            
            $this->assertInstanceOf(ModelNotFoundException::class, $e);
        }
    }   

    public function test_convert()
    {
        $result = false;
        $currencyConverterRepository = $this->app->make(CurrencyConverterRepositoryInterface::class);
        $rate = $currencyConverterRepository->convert(self::CURRENCY, self::CURRENCY_2, date('Y-m-d'));

        if (isset($rate['rate']))
        {
            $result = true;
        }

        $this->assertTrue($result);
    }

    public function test_unknown_convert()
    {
        try {                        
            $currencyConverterRepository = $this->app->make(CurrencyConverterRepositoryInterface::class);
            $currencyConverterRepository->convert('X', 'Y', date('Y-m-d'));            
            $this->assertTrue(false);
        } catch (Throwable $e) {            
            $this->assertTrue(true);
        }
    }
}