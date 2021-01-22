<?php
namespace EmizorIpx\ClientFel\Utils;

use EmizorIpx\ClientFel\Models\FelRequestLog;
use Illuminate\Http\Request;

class Log {

    public static function Homologate(Request $request) 
    {
        $log = [
            "entity" => "\EmizorIpx\ClientFel\Models\FelSyncProduct::class",
            "entity_id" => 0,
            "request" => $request->all()
        ];

        FelRequestLog::create($log);

    }

    public static function createInvoice(Request $request) 
    {
        $log = [
            "entity" => "\EmizorIpx\ClientFel\Models\FelInvoice::class",
            "entity_id" => 0,
            "request" => $request->all()
        ];

        FelRequestLog::create($log);                
    }
}