<?php

namespace EmizorIpx\ClientFel\Repository;

use EmizorIpx\ClientFel\Models\FelClient;
use EmizorIpx\ClientFel\Repository\Interfaces\RepoInterface;
use Exception;
use Hashids\Hashids;

class FelInvoiceRepository extends BaseRepository implements RepoInterface
{

    public function create($fel_data, $model)
    {

        bitacora_info("FelInvoiceRepository:create", json_encode($fel_data));

        try {
            $this->parseFelData($fel_data);

            $client = FelClient::where('id_origin', $model->client_id)->find();

            $user = $model->user;

            $line_items = $model->line_items;

            $total = 0;
            
            foreach ($line_items as $detail) {

                $new = new stdClass;

                $hashid = new Hashids(config('ninja.hash_salt'), 10);

                $new->codigoProducto =  $hashid->decode($detail->product_id)[0] . ""; // this values was added only frontend Be careful
                $new->descripcion = $detail->notes;
                $new->precioUnitario = $detail->cost;
                $new->subTotal = $detail->quantity * $detail->cost;
                $new->cantidad = $detail->quantity;
                $new->numeroSerie = null;
                if ($detail->discount > 0)
                    $new->montoDescuento = $detail->discount;
                $new->numeroImei = null;
                $new->unidadMedida = $detail->custom_value2;
                $details[] = (array)$new;

                $total += $new->subTotal;
            }



            $input = [
                #fel fata
                "codigoMetodoPago" => $this->fel_data_parsed['payment_method_id'],
                "codigoLeyenda" => $this->fel_data_parsed['caption_id'],

                #automatico
                "numeroFactura" => $model->number,

                # it is generated in FEL
                // "fechaEmision" => $dateISOFormatted,
                
                "nombreRazonSocial" => $client->business_name,
                "codigoTipoDocumentoIdentidad" => $client->type_document_id,
                "numeroDocumento" => $client->document_number,
                "complemento" => null,
                "codigoCliente" => $client->id_origin . "",
                "emailCliente" => null,
                "telefonoCliente" => $model->client->phone,
                
                
                "codigoPuntoVenta" => 1,
                "numeroTarjeta" => null,
                "codigoMoneda" => 1,
                "extras" => null,
                "tipoCambio" => 1,
                
                "montoTotal" => $total,
                "montoTotalMoneda" => $total,
                "montoTotalSujetoIva" => $total,
                
                "usuario" => $user->first_name . " " . $user->last_name,
                
                "detalles" => $details
            ];

          
        } catch (Exception $ex) {
            bitacora_error("FelInvoiceRepository:create", $ex->getMessage());
        }
    }

    public function update($fel_data, $model)
    {
    }
    public function delete($model)
    {
    }
}
