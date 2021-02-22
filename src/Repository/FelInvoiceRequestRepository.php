<?php

namespace EmizorIpx\ClientFel\Repository;

use Carbon\Carbon;
use EmizorIpx\ClientFel\Models\FelClient;
use EmizorIpx\ClientFel\Models\FelInvoiceRequest;
use EmizorIpx\ClientFel\Models\FelSyncProduct;
use EmizorIpx\ClientFel\Repository\Interfaces\RepoInterface;
use Exception;
use Hashids\Hashids;
use stdClass;

class FelInvoiceRequestRepository extends BaseRepository implements RepoInterface
{

    public function create($fel_data, $model)
    {

        bitacora_info("FelInvoiceRequestRepository:create", json_encode($fel_data));
        
        try {
           
            $input = $this->processInput($fel_data, $model);
        
            FelInvoiceRequest::create($input);
          
        } catch (Exception $ex) {
            bitacora_error("FelInvoiceRequestRepository:create", "File: " . $ex->getFile() . " Line: " . $ex->getLine() . "Message: " . $ex->getMessage());
        }
    }

    public function update($fel_data, $model)
    {

        bitacora_info("FelInvoiceRequestRepository:update", json_encode($fel_data));

        try {
            if (request()->has('fel_data')) {

                $input = $this->processInput($fel_data, $model);
                $invoice_request = FelInvoiceRequest::whereIdOrigin($model->id)->first();
                    
                if (! is_null($invoice_request) ) {
                    $invoice_request->update($input);
                }
                
            }

        } catch (Exception $ex) {
            bitacora_error("FelInvoiceRequestRepository:update", $ex->getMessage());
        }
    }
    public function delete($model)
    {
        bitacora_info("FelInvoiceRequestRepository:delete", "");

        try {

            $invoice_request = FelInvoiceRequest::whereIdOrigin($model->id)->first();

            if (!is_null($invoice_request)) {
                $invoice_request->delete();
            }

        } catch (Exception $ex) {
            bitacora_error("FelInvoiceRepository:delete", $ex->getMessage());
        }
    }

    public function processInput( $fel_data, $model)
    {
        $this->parseFelData($fel_data);

        if (is_null($model)) {
            bitacora_error("FelInvoiceRepository:PROCESS model","MODEL INVOICE IS NULL");
        }


        $client = FelClient::where('id_origin', $model->client_id)->first();
        
        $user = $model->user;
        
        $line_items = $model->line_items;
        
        $total = 0;

        $hashid = new Hashids(config('ninja.hash_salt'), 10);

        foreach ($line_items as $detail) {

            $id_origin = $hashid->decode($detail->product_id)[0];
        
            $product_sync = FelSyncProduct::whereIdOrigin($id_origin)->whereCompanyId($model->company_id)->first();
            
            $new = new stdClass;
            $new->codigoProducto =  $product_sync->codigo_producto . ""; // this values was added only frontend Be careful
            $new->codigoProductoSin =  $product_sync->codigo_producto_sin . ""; // this values was added only frontend Be careful
            $new->descripcion = $detail->notes;
            $new->precioUnitario = $detail->cost;
            $new->subTotal = $detail->quantity * $detail->cost;
            $new->cantidad = $detail->quantity;
            $new->numeroSerie = null;

            if ($detail->discount > 0)
                $new->montoDescuento = $detail->discount;

            $new->numeroImei = null;

            $new->unidadMedida = $product_sync->codigo_unidad;

            $details[] = $new;

            $total += $new->subTotal;
        }


        $input = [
            "id_origin" => $model->id,
            "company_id" => $model->company_id,
            #fel fata
            "codigoMetodoPago" => $this->fel_data_parsed['payment_method_id'],
            "codigoLeyenda" => $this->fel_data_parsed['caption_id'],
            "codigoActividad" => $this->fel_data_parsed['activity_id'],

            #automatico
            "numeroFactura" => $model->number ?? 0,

            # it is generated in FEL
            "fechaEmision" => substr( Carbon::parse(Carbon::now())->format('Y-m-d\TH:i:s.u'), 0, -3),

            "nombreRazonSocial" => $client->business_name,
            "codigoTipoDocumentoIdentidad" => $client->type_document_id,
            "numeroDocumento" => $client->document_number,
            "complemento" => null,
            "codigoCliente" => $client->id_origin . "",
            "emailCliente" => null,
            "telefonoCliente" => $model->client->phone,


            "codigoPuntoVenta" => 0,
            "numeroTarjeta" => null,
            "codigoMoneda" => 1,
            "extras" => null,
            "tipoCambio" => 1,

            "montoTotal" => $total,
            "montoTotalMoneda" => $total,
            "montoTotalSujetoIva" => $total,

            "usuario" => $user->first_name . " " . $user->last_name,

            "detalles" => json_encode($details)
        ];

        return $input;
    }
}
