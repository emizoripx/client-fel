<?php

namespace EmizorIpx\ClientFel\Traits;

use EmizorIpx\ClientFel\Exceptions\ClientFelException;
use EmizorIpx\ClientFel\Models\FelClientToken;
use EmizorIpx\ClientFel\Services\Invoices\Invoices;
use EmizorIpx\ClientFel\Utils\TypeDocuments;
use Illuminate\Support\Facades\Log;
use stdClass;

class InvoiceTrait
{

    public function createInvoiceFel($data)
    {

        $client = $this->client;

        $user = $this->user;
    
        $line_items = $this->line_items;

        foreach($line_items as $detail) {
            
            $new = new stdClass;

            $new->codigoProducto = $detail->product_key;
            $new->descripcion = $detail->description;
            $new->precioUnitario = $detail->cost;
            $new->subTotal = $detail->quantity * $detail->cost;
            $new->cantidad = $detail->quantity;
            $new->numeroSerie =null;
            $new->montoDescuento =$detail->discount;
            $new->numeroImei =null;
            $new->unidaMedida = $detail->custom_value2;
            
            array_push($details, $new);

        }

        $data = [

            "numeroFactura" => $this->number,
            "codigoPuntoVenta" => 0,
            "fechaEmision" => $this->date,
            "nombreRazonSocial" => $client->name,
            "codigoTipoDocumentoIdentidad" => $client->custom_value1,
            "numeroDocumento" => $client->vat_number,
            "complemento" => null,
            "codigoCliente" => $client->id,
            "codigoMetodoPago" => 1,
            "numeroTarjeta" => null,
            "montoTotal" => $this->amount,
            "codigoMoneda" => 1,
            "montoTotalMoneda" => $this->amount,
            "usuario" => $user->first_name . " " . $user->last_name,
            "emailCliente" => null,
            "telefonoCliente" => $client->phone,
            "extras" => null,
            "codigoLeyenda" => $this->custom_value1,
            "montoTotalSujetoIva" => $this->amount,
            "tipoCambio" => 1 ,
            "details" => $details      
        ];


        try{

            $access_token = FelClientToken::getTokenByAccount($this->company_id);
            
            $invoice_service = new Invoices;
            
            $invoice_service->setAccessToken($access_token);
            
            $invoice_service->setBranchNumber(0);
            
            $invoice_service->setData($data);
            
            $invoice_service->setTypeDocument(TypeDocuments::COMPRA_VENTA);

            $invoice_service->send();

        } catch(ClientFelException $ex) {

            Log::debug("problems  " . json_encode($ex->getMessage()) );
        }


    }
}
