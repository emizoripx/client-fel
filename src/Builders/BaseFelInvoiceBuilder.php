<?php
namespace EmizorIpx\ClientFel\Builders;

use Carbon\Carbon;
use EmizorIpx\ClientFel\Models\FelInvoiceRequest;
use EmizorIpx\ClientFel\Models\Parametric\SectorDocumentTypes;
use EmizorIpx\ClientFel\Utils\TypeInvoice;
use Exception;

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
        $client_email_first_invitation= "";
        try{
            // hardcoded for msc
            
            $invitation = $model->invitations()->first(); 
            if ( !empty($invitation)) {
                $client_email_first_invitation = $invitation->contact->email;
            }
            
        } catch (Exception $ex) {
            // \Log::debug("==========================================================================");
            \Log::debug($ex->getMessage());
            // \Log::debug("==========================================================================");
        }
        try {
            
            $fechadeemision = isset($fel_data_parsed['fechaDeEmision']) ? Carbon::parse($fel_data_parsed['fechaDeEmision']) : Carbon::parse(Carbon::now());
        } catch (\Throwable $th) {
            $fechadeemision = Carbon::parse(Carbon::now());
        }

        if ( in_array($fel_data_parsed['numeroDocumento'], [99001]) ) {
            $fel_data_parsed['codigoTipoDocumentoIdentidad'] = 4;
            $fel_data_parsed['complemento'] = null;
            $client->complement = null;
        }
        if ( in_array($fel_data_parsed['numeroDocumento'], [99002]) ) {
            $fel_data_parsed['nombreRazonSocial'] = "CONTROL TRIBUTARIO";
            $fel_data_parsed['codigoTipoDocumentoIdentidad'] = 4;
            $fel_data_parsed['complemento'] = null;
            $client->complement = null;
        }
        if ( in_array($fel_data_parsed['numeroDocumento'], [99003]) ) {
            $fel_data_parsed['nombreRazonSocial'] = "VENTAS MENORES DEL DÍA";
            $fel_data_parsed['codigoTipoDocumentoIdentidad'] = 4;
            $fel_data_parsed['complemento'] = null;
            $client->complement = null;
        }

        $this->input = array_merge($this->input ,[
            "id_origin" => $model->id,
            "company_id" => $model->company_id,
            "type_document_sector_id" => $fel_data_parsed['type_document_sector_id'],
            "type_invoice" => ucwords(strtolower(TypeInvoice::getTypeInvoice($fel_data_parsed['type_document_sector_id']))),
            #fel fata
            "codigoMetodoPago" => $fel_data_parsed['payment_method_id'],
            "numeroTarjeta" => $fel_data_parsed['numero_tarjeta'],
            "codigoLeyenda" => isset($fel_data_parsed['caption_id']),
            "codigoActividad" => $fel_data_parsed['activity_id'],
            "codigoExcepcion" => $fel_data_parsed['codigoExcepcion'],
            #automatico
            "numeroFactura" => $fel_data_parsed['numeroFactura'] ? $fel_data_parsed['numeroFactura'] : ($model->number ?? 0),
            # it is generated in FEL
            "fechaEmision" => substr($fechadeemision->setTimezone('America/La_Paz')->format('Y-m-d\TH:i:s.u'), 0, -3),
            "codigoPuntoVenta" => $fel_data_parsed['codigoPuntoVenta'],
            "codigoSucursal" => $fel_data_parsed['codigoSucursal'],
            "usuario" => trim($user->first_name . " " . $user->last_name) != "" ? trim($user->first_name . " " . $user->last_name) : "Usuario Genérico",
            "extras" => json_encode($fel_data_parsed['extras']),
            "codigoMoneda" => $fel_data_parsed['codigo_moneda'],
            //clientdata
            "nombreRazonSocial" => is_null($fel_data_parsed['nombreRazonSocial']) ?  $client->business_name : $fel_data_parsed['nombreRazonSocial'],
            "codigoTipoDocumentoIdentidad" => is_null($fel_data_parsed['codigoTipoDocumentoIdentidad']) ?  $client->type_document_id : $fel_data_parsed['codigoTipoDocumentoIdentidad'],
            "numeroDocumento" => is_null($fel_data_parsed['numeroDocumento']) ? $client->document_number : $fel_data_parsed['numeroDocumento'],
            "complemento" => $fel_data_parsed['complemento'],
            "codigoCliente" => $model->client->number,
            "emailCliente" => $client_email_first_invitation != "" ? $client_email_first_invitation : null,
            "telefonoCliente" => $model->client->phone,
            "typeDocument" => $fel_data_parsed['typeDocument'],
        ]);
        
    }

    public  function changeOriginalTotal( FelInvoiceRequest $fel_invoice_request)
    {
        \Log::debug("ingresando a recalculate totals");
        try {
            $model = $this->source_data['model'];
    
            $model->amount = $fel_invoice_request->montoTotal;
    
            $model->saveQuietly();
            
            \Log::debug("se registro existosamente el recalculo de los totales");


        } catch (\Throwable $th) {
            \Log::error("ERROR EN  " . $th->getMessage());
        }

    }

}