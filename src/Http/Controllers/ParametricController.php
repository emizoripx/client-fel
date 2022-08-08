<?php

namespace EmizorIpx\ClientFel\Http\Controllers;

use App\Http\Controllers\BaseController;
use EmizorIpx\ClientFel\Exceptions\ClientFelException;
use EmizorIpx\ClientFel\Models\FelParametric;
use EmizorIpx\ClientFel\Services\Parametrics\Parametric;
use Illuminate\Http\Request;

class ParametricController extends BaseController
{

    public function index(Request $request, $type)
    {
        
        $success = false;
        try {

            $response = FelParametric::index($type, $request->company_id);
            if (empty($response)) {

                $parametricService = new Parametric($request->access_token, $request->host);
                $parametricService->get($type);

                FelParametric::create($type, $parametricService->getResponse(), $request->company_id);
                
                $success = true;
                $response = FelParametric::index($type, $request->company_id);
            }

            return response()->json([
                "success" => $success,
                "data" => $response
            ]);

        } catch (ClientFelException $ex) {
            return response()->json([
                "success" => false,
                "msg" => $ex->getMessage()
            ]);
        }
    }
}
