<?php

namespace EmizorIpx\ClientFel\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ClientController extends Controller
{

    public function checkCode(Request $request)
    {
        $code = $request->get('code', null);

        if (!is_null($code) && \DB::table('clients')->where("company_id",$request->company_id)->where('number', $code)->exists()) {
            return response()->json(["message" => "El código de cliente ya fue registrado"], 400);
        }

        return response()->json(["data" => ["message" => "El código de cliente esta disponible, no se registró."]], 200);
    }
}