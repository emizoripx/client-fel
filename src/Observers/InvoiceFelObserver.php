<?php

namespace EmizorIpx\ClientFel\Observers;

use EmizorIpx\ClientFel\Exceptions\ClientFelException;
use EmizorIpx\ClientFel\Models\FelClientToken;
use EmizorIpx\ClientFel\Services\Invoices\Invoices;
use EmizorIpx\ClientFel\Utils\TypeDocuments;
use Illuminate\Support\Facades\Log;
use stdClass;

class InvoiceFelObserver
{
    public function creating($model) 
    {

        \Log::debug("invoicemodel ; " . json_encode($model));
        $access_token = FelClientToken::getTokenByAccount($model->company_id);

        $client = $model->client;

        $user = $model->user;
    
        $line_items = $model->line_items;

        $total = 0;

        foreach($line_items as $detail) {
            
            $new = new stdClass;

            $new->codigoProducto = $detail->product_key;
            $new->descripcion = $detail->notes;
            $new->precioUnitario = $detail->cost;
            $new->subTotal = $detail->quantity * $detail->cost;
            $new->cantidad = $detail->quantity;
            $new->numeroSerie =null;
            if ($detail->discount > 0)
                $new->montoDescuento =$detail->discount;
            $new->numeroImei =null;
            $new->unidadMedida = $detail->custom_value2;
            $total += $new->subTotal;
            $details[] = (array)$new;

        }

        $data = [

            "numeroFactura" => $model->number,
            "codigoPuntoVenta" => 1,
            "fechaEmision" => $model->date,
            "nombreRazonSocial" => $client->name,
            "codigoTipoDocumentoIdentidad" => $client->custom_value1,
            "numeroDocumento" => $client->id_number,
            "complemento" => null,
            "codigoCliente" => $client->id."",
            "codigoMetodoPago" => $model->custom_value3,
            "numeroTarjeta" => null,
            "montoTotal" => $total,
            "codigoMoneda" => 1,
            "montoTotalMoneda" => $total,
            "usuario" => $user->first_name . " " . $user->last_name,
            "emailCliente" => null,
            "telefonoCliente" => $client->phone,
            "extras" => null,
            "codigoLeyenda" => $model->custom_value1,
            "montoTotalSujetoIva" => $total,
            "tipoCambio" => 1 ,
            "detalles" => $details      
        ];

        Log::debug(" creating invoices " . json_encode($data));
        try{
                        
            $invoice_service = new Invoices;
            
            $invoice_service->setAccessToken($access_token);
            
            $invoice_service->setBranchNumber(0);
            
            $invoice_service->setData($data);
            
            $invoice_service->setTypeDocument(TypeDocuments::COMPRA_VENTA);

            $invoice_service->send();

            return true;

        } catch(ClientFelException $ex) {
            Log::debug("problems  " . json_encode($ex->getMessage()) );
            throw $ex;

        }
        
    }
}