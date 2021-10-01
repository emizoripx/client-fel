<?php
namespace EmizorIpx\ClientFel\Builders;

use Carbon\Carbon;
use EmizorIpx\ClientFel\Models\Parametric\SectorDocumentTypes;
use EmizorIpx\ClientFel\Utils\TypeInvoice;

class BaseFelInvoiceBuilder {

    protected $source_data;

    protected $input = array();

    public function __construct($source_data)
    {
        $this->source_data = $source_data;    
        $this->setStaticInput();
    }

    public function setStaticInput() : void
    {


        $model = $this->source_data['model'];
        $fel_data_parsed = $this->source_data['fel_data_parsed'];
        $user = $this->source_data['user'];
        $client = $this->source_data['client'];


        $this->input = array_merge($this->input ,[
            "id_origin" => $model->id,
            "company_id" => $model->company_id,
            "type_document_sector_id" => $fel_data_parsed['type_document_sector_id'],
            "type_invoice" => ucwords(strtolower(TypeInvoice::getTypeInvoice($fel_data_parsed['type_document_sector_id']))),
            #fel fata
            "codigoMetodoPago" => $fel_data_parsed['payment_method_id'],
            "numeroTarjeta" => $fel_data_parsed['numero_tarjeta'],
            "codigoLeyenda" => $fel_data_parsed['caption_id'],
            "codigoActividad" => $fel_data_parsed['activity_id'],
            #automatico
            "numeroFactura" => $model->number ?? 0,
            # it is generated in FEL
            "fechaEmision" => substr(Carbon::parse(Carbon::now())->setTimezone('America/La_Paz')->format('Y-m-d\TH:i:s.u'), 0, -3),
            "codigoPuntoVenta" => $fel_data_parsed['codigoPuntoVenta'],
            "codigoSucursal" => $fel_data_parsed['codigoSucursal'],
            "usuario" => trim($user->first_name . " " . $user->last_name) != "" ? trim($user->first_name . " " . $user->last_name) : "Usuario Genérico",
            "extras" => $fel_data_parsed['extras'],
            "codigoMoneda" => $fel_data_parsed['codigo_moneda'],
            //clientdata
            "nombreRazonSocial" => $client->business_name,
            "codigoTipoDocumentoIdentidad" => $client->type_document_id,
            "numeroDocumento" => $client->document_number,
            "complemento" => $client->complement ?? null,
            "codigoCliente" => $model->client->number,
            "emailCliente" => null,
            "telefonoCliente" => $model->client->phone
        ]);
        
    }

}