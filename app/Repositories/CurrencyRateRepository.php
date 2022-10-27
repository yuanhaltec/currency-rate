<?php

namespace App\Repositories;

use App\Models\CurrencyRate;
use App\Services\Currency\CurrencyConverterService;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CurrencyRateRepository implements CurrencyRateRepositoryInterface
{
    protected $model;
    protected $currencyRepository;
    protected $currencyConverterService;

    public function __construct(
        CurrencyRate $currencyRate,
        CurrencyRepository $currencyRepository,
        CurrencyConverterService $currencyConverterService
    )
    {
        $this->model = $currencyRate;
        $this->currencyRepository = $currencyRepository;
        $this->currencyConverterService = $currencyConverterService;
    }

    public function rate($from, $to, $date)
    {
        $fromCurrency = $this->currencyRepository->findByCurrency($from);

        if (!$fromCurrency) {
            throw new ModelNotFoundException('Currency '.$from.' not found');
        }

        $toCurrency = $this->currencyRepository->findByCurrency($to);
        
        if (!$toCurrency) {
            throw new ModelNotFoundException('Currency '.$to.' not found');
        }
        
        $rate = $this->findRate($fromCurrency->id, $toCurrency->id, $date);
        
        if ($rate) {
            return $rate;
        }

        $result = $this->currencyConverterService->convert($fromCurrency->currency, $toCurrency->currency, $date);
        $this->create([
            'date' => $date,
            'from_id' => $fromCurrency->id,
            'to_id' => $toCurrency->id,
            'rate' => $result['rate'],
            'created_by' => 0,
            'updated_by' => 0
        ]);
        
        return $this->findRate($fromCurrency->id, $toCurrency->id, $date);
    }

    public function findRate($from, $to, $date)
    {
        return $this->model->where('from_id', $from)
            ->where('to_id', $to)
            ->where('date', $date)
            ->first();
    }

    public function create($rate) {        
        return $this->model->create($rate);
    }
}