<?php

namespace App\Repositories;

use App\Models\Currency;
use Illuminate\Database\Eloquent\Collection;

class CurrencyRepository implements CurrencyRepositoryInterface
{
    protected $model;

    public function __construct(Currency $currency)
    {
        $this->model = $currency;
    }

    public function get(): Collection
    {
        return $this->model->all();
    }

    public function find(int $id): ?Currency
    {
        return $this->model->find($id);
    }

    public function create(string $currency): Currency
    {
        return $this->model->create([
            'currency' => $currency,
            'created_by' => 0,
            'updated_by' => 0
        ]);
    }

    public function update(int $id, string $currency): bool
    {
        return $this->model->where('id', $id)
            ->update(['currency' => $currency]);
    }

    public function delete(int $id): bool
    {
        return $this->model->where('id', $id)
            ->delete();
    }

    public function findByCurrency(string $currency): ?Currency
    {
        return $this->model->where('currency', $currency)
            ->first();
    }
}