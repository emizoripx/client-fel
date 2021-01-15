<?php

namespace EmizorIpx\ClientFel\Http\Controllers;

use EmizorIpx\ClientFel\Exceptions\ClientFelException;
use EmizorIpx\ClientFel\Http\Requests\StoreCredentialsRequest;
use EmizorIpx\ClientFel\Models\FelClientToken;
use EmizorIpx\ClientFel\Models\FelSyncProduct;
use EmizorIpx\ClientFel\Services\Products\Products;
use Hamcrest\Type\IsNumeric;
use Hashids\Hashids;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ProductController extends Controller
{

    public function homologate(Request $request)
    {

        $input = $request->only(['codigo_producto', 'codigo_producto_sin']);

        if ($input['codigo_producto'] && $input['codigo_producto'] == "") {
            $error[] = "codigo_producto is required";
        }

        if ($input['codigo_producto_sin'] && $input['codigo_producto_sin'] == "") {
            $error[] = "codigo_producto_sin is required";
        }

        if (!empty($error)) {
            return response()->json(['success' => false, "msg" => $error]);
        }

        $input['codigoProducto'] = $input['codigo_producto'];
        if ( !is_numeric($input['codigo_producto']) ) {
            $hashids = new Hashids();
            $input['codigoProducto'] = $hashids->decode($input['codigo_producto']);    
        }
        $input['codigoProductoSIN'] = $input['codigo_producto_sin'];

        try {
            
            $productService = new Products($request->access_token);
            $response = $productService->homologate($input);
            $product_sync = FelSyncProduct::create($response);

            return response()->json([
                "success" => true,
                "product_sync" => $product_sync
            ]);

        } catch (ClientFelException $ex) {
            return response()->json([
                "success" => false,
                "msg" => $ex->getMessage()
            ]);
        }
    }
}
