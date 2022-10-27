<?php

namespace App\Services\Currency;

use Exception;
use Illuminate\Support\Facades\Http;

class CurrencyConverterService implements CurrencyConverterServiceInterface
{
    public function convert($from, $to, $date) {
        $url = sprintf(config('param.currency_api_url').'?from=%s&to=%s&date=%s&amount=1', $from, $to, $date);          
        $response = Http::withHeaders(['apikey' => config('param.currency_api_key')])->get($url);

        if ($response->getStatusCode() != 200) {
            $responseData = json_decode($response->getBody()->getContents(), true);            
            throw new Exception('Can\'t connect to '.config('param.currency_api_url'));
        }

        $responseData = json_decode($response->getBody()->getContents(), true);
        if (isset($responseData['error'])) {
            throw new Exception($responseData['error']['info']);
        }      
                
        $rate = rtrim(sprintf('%.10f', floatval($responseData['result'])), 0);    
        $result = [
            'date' => $date,
            'from' => $from,
            'to' => $to,
            'rate' => $rate
        ];
        return $result;
    }
}