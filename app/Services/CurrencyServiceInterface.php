<?php

namespace App\Services;

use Illuminate\Http\Request;

interface CurrencyServiceInterface
{
    public function get();
    public function create(Request $request);
}