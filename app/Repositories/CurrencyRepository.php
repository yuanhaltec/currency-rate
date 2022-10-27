<?php

namespace App\Repositories;

use App\Models\Currency;

class CurrencyRepository implements CurrencyRepositoryInterface
{
    protected $model;

    public function __construct(Currency $currency)
    {
        $this->model = $currency;
    }

    public function get()
    {
        return $this->model->all();
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function create($currency)
    {
        return $this->model->create([
            'currency' => $currency,
            'created_by' => 0,
            'updated_by' => 0
        ]);
    }

    public function update($id, $currency)
    {
        return $this->model->where('id', $id)
            ->update(['currency' => $currency]);
    }

    public function delete($id)
    {
        return $this->model->where('id', $id)
            ->delete();
    }

    public function findByCurrency($currency)
    {
        return $this->model->where('currency', $currency)
            ->first();
    }
}