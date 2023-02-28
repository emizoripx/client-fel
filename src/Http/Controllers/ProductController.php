<?php

namespace EmizorIpx\ClientFel\Http\Controllers;

use EmizorIpx\ClientFel\Exceptions\ClientFelException;
use EmizorIpx\ClientFel\Services\Products\Products;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ProductController extends Controller
{

    public function checkCode(Request $request)
    {
        $code = $request->get('code', null);

        if (!is_null($code) && \DB::table('fel_sync_products')->where('company_id', $request->company_id)->where('codigo_producto', $code)->exists()) {
            return response()->json(["message" => "El código de producto ya fue registrado"], 400);
        }
        return response()->json(["data" => ["message" => "El código de producto esta disponible, no se registró."]], 200);
    }

    public function homologate(Request $request)
    {

        $input = $request->only(['codigo_producto', 'codigo_producto_sin', 'codigo_unidad', 'nombre_unidad']);

        try {
            
            $productService = new Products($request->access_token, $request->host);
            
            $productService->setData($input);
            
            $productService->homologate();

            $productService->saveResponse();

            return response()->json([
                "success" => true,
                "product_sync" => $productService->getResponse()
            ]);

        } catch (ClientFelException $ex) {
            return response()->json([
                "success" => false,
                "msg" => $ex->getMessage()
            ]);
        }
    }
}
