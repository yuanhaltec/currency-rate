<?php

namespace App\Repositories;

interface CurrencyRepositoryInterface
{
    public function get();
    public function find(int $id);
}