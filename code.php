<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;

class BinanceController extends Controller
{
    public function getMacdData()
    {
        set_time_limit(1000);
        $tradingPairs = $this->getUSDTTradingPairs();

        $results = [];

        foreach ($tradingPairs as $symbol) {
          $price = $this->getHistoricalData($symbol);
          $results[$symbol] = $price;

        }
        return response()->json($results);
    }

    // Function to get trading pairs with USDT
    private function getUSDTTradingPairs()
    {
        $exchangeInfo = $this->getExchangeInfo();
        $usdtPairs = [];

        foreach ($exchangeInfo['symbols'] as $symbol) {
            if (strpos($symbol['symbol'], 'USDT') !== false) {
                $usdtPairs[] = $symbol['symbol'];
            }
        }

        return $usdtPairs;
    }

    private function getExchangeInfo()
    {
        $url = "https://api.binance.com/api/v3/exchangeInfo";
        $response = Http::get($url);

        return $response->json();
    }
    private function getHistoricalData($symbol)
    {
        // Set the start time to one week ago from the current time
        $startTime = strtotime('-1 week') * 1000;

        // Set the end time to the current time
        $endTime = time() * 1000;

        $url = "https://api.binance.com/api/v3/klines";
        $parameters = [
            'symbol' => $symbol,
            'interval' => '1h',
            'startTime' => $startTime,
            'endTime' => $endTime,
        ];

        $response = Http::get($url, $parameters);
        $responseData = $response->json();

        // Check if the response data is an array and not empty
        if (is_array($responseData) && !empty($responseData)) {
            return $responseData;
        } else {
            // Handle the case where the response data is empty or not an array
            // You can log an error or return an empty array, depending on your needs
            return [];
        }
    }


}
