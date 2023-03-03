<?php

namespace App\Repositories;

use Exception;
use Illuminate\Support\Facades\Http;

class CurrencyConverterRepository implements CurrencyConverterRepositoryInterface
{
    protected $config;    

    public function __construct()
    {
        $this->config([
            'url' => config('param.currencyApi.url'),
            'key' => config('param.currencyApi.key')
        ]);
    }

    public function config(?array $config = null) {
        if ($config) {
            if (!empty($config['url'])) {
                $this->config['url'] = $config['url'];
            }

            if (!empty($config['key'])) {
                $this->config['key'] = $config['key'];
            }
        }

        return $this->config;
    }

    public function convert(string $from, string $to, string $date): array {
        $url = sprintf($this->config['url'].'?from=%s&to=%s&date=%s&amount=1', $from, $to, $date);          
        $response = Http::withHeaders(['apikey' => $this->config['key']])->get($url);
        
        if ($response->getStatusCode() != 200) {
            $responseData = json_decode($response->getBody()->getContents(), true);            
            throw new Exception('Can\'t connect to '.$this->url);
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