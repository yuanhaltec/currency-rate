<?php

namespace App\Services;

use App\Models\Currency;
use App\Models\CurrencyRate;
use App\Repositories\CurrencyRateRepositoryInterface;
use App\Repositories\CurrencyRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CurrencyService implements CurrencyServiceInterface
{
    protected $currencyRepository;
    protected $currencyRateRepository;

    public function __construct(
        CurrencyRepositoryInterface $currencyRepository,
        CurrencyRateRepositoryInterface $currencyRateRepository
    )
    {
        $this->currencyRepository = $currencyRepository;
        $this->currencyRateRepository = $currencyRateRepository;
    }

    public function get(): Collection
    {
        return $this->currencyRepository->get();
    }

    public function create(Request $request): Currency
    {
        $this->validation($request);
        return $this->currencyRepository->create($request->currency);
    }

    public function update(Request $request, $id): bool
    {
        $currency = $this->currencyRepository->find($id);

        if (!$currency) {
            throw new ModelNotFoundException('Data currency tidak ditemukan');
        }

        $this->validation($request, $currency);
        return $this->currencyRepository->update($id, $request->currency);
    }

    public function delete($id): bool
    {        
        $currency = $this->currencyRepository->find($id);

        if (!$currency) {
            throw new ModelNotFoundException('Data currency tidak ditemukan');
        }

        return $this->currencyRepository->delete($id);
    }

    public function rate(Request $request, $from, $to): CurrencyRate
    {
        $date = $request->get('date') ?? date('Y-m-d');
        $from = strtoupper($from);
        $to = strtoupper($to);
        $rate = $this->currencyRateRepository->rate($from, $to, $date);
        return $rate;
    }

    protected function validation(Request $request, ?Currency $currency = null): bool {
        if ($currency) {
            $validator = Validator::make($request->all(), [
                'currency' => [
                    'required',
                    Rule::unique('App\Models\Currency')->ignore($currency)
                ]
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                'currency' => 'required|unique:App\Models\Currency'
            ]);
        }
        
        if ($validator->fails()) {            
            throw new ValidationException($validator);
        }

        return true;
    }
}