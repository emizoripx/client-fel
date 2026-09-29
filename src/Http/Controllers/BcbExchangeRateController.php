<?php

namespace EmizorIpx\ClientFel\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class BcbExchangeRateController extends Controller
{
    public function getExchangeRate()
    {
        $cacheKey = 'bcb_exchange_rate_' . date('Y-m-d');
        
        $exchangeRates = Cache::remember($cacheKey, 43200, function () {
            return $this->fetchFromBcb();
        });

        if ($exchangeRates) {
            return response()->json([
                'success' => true,
                'data' => $exchangeRates
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No se pudo obtener el tipo de cambio referencial'
        ], 500);
    }

    private function fetchFromBcb()
    {
        $today = date('d/m/Y');
        
        $soapBody = '<?xml version="1.0" encoding="utf-8"?>
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ser="http://ws.bcb.gob.bo">
   <soapenv:Header/>
   <soapenv:Body>
      <ser:obtenerIndicador>
         <codIndicador>1</codIndicador>
         <codMoneda>75</codMoneda>
         <fecha>' . $today . '</fecha>
      </ser:obtenerIndicador>
   </soapenv:Body>
</soapenv:Envelope>';

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'text/xml;charset=UTF-8',
                'SOAPAction' => ''
            ])->send('POST', 'https://indicadores.bcb.gob.bo/ServiciosBCB/indicadores', [
                'body' => $soapBody
            ]);

            if ($response->successful()) {
                $xml = simplexml_load_string($response->body());
                if ($xml !== false) {
                    $xml->registerXPathNamespace('ns2', 'http://ws.bcb.gob.bo');
                    $xml->registerXPathNamespace('soap', 'http://schemas.xmlsoap.org/soap/envelope/');

                    $returns = $xml->xpath('//return');
                    
                    $valor = null;
                    foreach ($returns as $return) {
                        if ((string) $return->codDato === 'Valor') {
                            $valor = (float) $return->dato;
                            break;
                        }
                    }

                    if ($valor !== null) {
                        return [
                            'officialBuy' => 6.86,
                            'officialSell' => 6.96,
                            'referentialBuy' => $valor,
                            'referentialSell' => round($valor + 0.10, 2),
                            'dateFormatted' => $today,
                        ];
                    }
                }
            }
        } catch (\Exception $e) {
            \Log::error('[BcbExchangeRateController] Error fetching BCB indicator: ' . $e->getMessage());
        }

        return null;
    }
}
