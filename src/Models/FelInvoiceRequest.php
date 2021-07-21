<?php

namespace EmizorIpx\ClientFel\Models;


use Carbon\Carbon;
use EmizorIpx\ClientFel\Exceptions\ClientFelException;
use EmizorIpx\ClientFel\Services\Invoices\Invoices;
use EmizorIpx\ClientFel\Traits\DecodeHashIds;
use EmizorIpx\ClientFel\Traits\GetCredentialsTrait;
use EmizorIpx\ClientFel\Traits\GetInvoiceStateTrait;
use EmizorIpx\ClientFel\Traits\InvoiceUpdateDateTrait;
use EmizorIpx\PrepagoBags\Exceptions\PrepagoBagsException;
use EmizorIpx\PrepagoBags\Models\AccountPrepagoBags;
use EmizorIpx\PrepagoBags\Services\AccountPrepagoBagService;
use Hashids\Hashids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

class FelInvoiceRequest extends Model
{
    use DecodeHashIds;
    use GetCredentialsTrait;
    use GetInvoiceStateTrait;
    use InvoiceUpdateDateTrait;

    use SoftDeletes;

    protected $table = "fel_invoice_requests";

    protected $guarded = [];


    protected $casts =[
        'detalles' => 'array',
        'otrosDatos' => 'array',
        'costosGastosNacionales' => 'array',
        'costosGastosInternacionales' => 'array',
    ];

    protected $access_token;
    protected $host;

    public function getDetallesAttribute()
    {
       return json_decode($this->attributes['detalles'],true);
    }

    public function saveCuf($value) 
    {
        $this->cuf = $value;
        return $this;
    }
    public function saveAckTicket($value) 
    {
        $this->ack_ticket = $value;
        $this->save();
    }
    public function saveUrlSin($value) 
    {
        $this->urlSin = $value;
        return $this;
    }
    public function getUrlSin() 
    {
        $url = $this->urlSin ?? "qr no valido";
        // returns a complete url, no needed of any env(), FEL is config to return completed
        return $url;
    }

    public static function findByIdOrigin($id_origin)
    {
        $hashid = new Hashids(config('ninja.hash_salt'), 10);

        $id_origin_decode = $hashid->decode($id_origin)[0];
        
        return self::whereIdOrigin($id_origin_decode)->first();
    }


    public static function getByCompanyId($company_id)
    {
        return self::withTrashed()->where('company_id', $company_id)->get();
    }

    public function saveState($value){
        $this->estado = $this->getInvoiceState($value);
        return $this;
    }

    public function saveEmisionDate($fechaEmision){
        $this->fechaEmision = $fechaEmision;
        return $this;
    }

    public function saveStatusCode($value){
        $this->codigoEstado = $value;
        return $this;
    }

    public function saveSINErrors($value){
        $this->errores = $value;
        return $this;
    }
    public function saveRevocationReasonCode($value){
        $this->revocation_reason_code = $value;
        return $this;
    }
    public function getRevocationReasonCode(){
        return $this->revocation_reason_code;
    }
    public function getDeletedAt(){
        return $this->deleted_at;
    }
    public function restoreInvoice(){
        $this->restore();
        return $this;
    }
    
    public function saveEmisionType(array $value)
    {
        $this->emission_type = 'Fuera de línea';

        if ($value['codigo']==1){
            $this->emission_type = 'En línea';
        }
        return $this;
    }

    public function saveInvoiceTypeId(array $value)
    {
        $this->type_invoice_id = 1;

        if ( !empty($value) ) {
            $this->type_invoice_id = $value['codigoTipoFactura'];
        }

        return $this;
    }

    public function saveAddressInvoice($value){
        
        $this->direccion = $value;

        return $this;
    }
    /**
     * Get the prepagoAccount instance
     *
     * 
     */
    public function felCompany()
    {
        $hashid = new Hashids(config('ninja.hash_salt'), 10);

        $company_id_decode = $hashid->decode($this->company_id)[0];
        $accountDetails = AccountPrepagoBags::where('company_id', $company_id_decode)->first();
        return $accountDetails;
    }

    public function sendInvoiceToFel(){

        try {

            // $prepagoBagService = new AccountPrepagoBagService();

            $detailCompanyDocumentSector = $this->felCompany()->service()->controlPrepagoBag($this->type_document_sector_id);
            
            // $prepagoBagService->controlPrepagoBag($this->prepagoAccount(), $this->type_document_sector_id);
            
        } catch (PrepagoBagsException $ex) {
            Log::debug('Fel Error');
            throw new ClientFelException($ex->getMessage());
        }
        
        $invoice_service = new Invoices($this->host);

        $invoice_service->setAccessToken($this->access_token);


        $invoice_service->setBranchNumber($this->codigoSucursal);

        $invoice_service->buildData($this);

        $invoice_service->sendToFel();

        $this->saveAckTicket($invoice_service->getResponse()['ack_ticket']);
        // $invoice_service->setCuf($invoice_service->getResponse()['cuf']);

        $invoice_service->setAckTicket($invoice_service->getResponse()['ack_ticket']);
        
        $invoice = $invoice_service->getInvoiceByAckTicket();
        \Log::debug("================================================================================");
        \Log::debug([$invoice_service->getResponse()]);
        \Log::debug("================================================================================");
        $this->saveState($invoice['estado'])
             ->saveCuf($invoice_service->getResponse()['cuf'])
            //TO-DO: un comment once, it is sent from  fel, nota_debito with url_sin
             //  ->saveUrlSin($invoice['urlSin'])
             ->saveUrlSin($invoice['urlSin']??"")
             ->saveEmisionDate($invoice['fechaEmision'])
             ->saveEmisionType($invoice['tipoEmision'])
             ->saveInvoiceTypeId($invoice['documentoSector'])
             ->saveAddressInvoice($invoice['direccion'])
             ->save();

        $this->invoiceDateUpdate();
        
        $account = $this->felCompany();
        if(!$account->checkIsPostpago()){
            $detailCompanyDocumentSector->reduceNumberInvoice()->setCounter()->save();
        }
    }


    public function sendRevocateInvoiceToFel($codigoMotivoAnulacion){
        $invoice_service = new Invoices($this->host);

        $invoice_service->setAccessToken($this->access_token);
        $invoice_service->setCuf($this->cuf);
        $invoice_service->setRevocationReasonCode($codigoMotivoAnulacion);

        $invoice_service->revocateInvoice();

        // $invoice = $invoice_service->getInvoiceByAckTicket();
        $invoice = $invoice_service->getInvoiceByCuf();

        $this->saveState($invoice['estado'])->saveRevocationReasonCode($codigoMotivoAnulacion)->save();
    }

    public function sendReversionRevocateInvoiceToFel(){
        $invoice_service = new Invoices($this->host);

        $invoice_service->setAccessToken($this->access_token);
        $invoice_service->setCuf($this->cuf);

        // $invoice = $invoice_service->getInvoiceByAckTicket();
        $invoice = $invoice_service->getInvoiceByCuf();

        $invoice = $invoice_service->getInvoiceByAckTicket();

        $this->saveState($invoice['estado'])->save();
    }

    public function sendUpdateInvoiceToFel(){
        $invoice_service = new Invoices($this->host);

        $invoice_service->setAccessToken($this->access_token);
        $invoice_service->setCuf($this->cuf);
        $invoice_service->setBranchNumber($this->codigoSucursal);

        $invoice_service->buildData($this);

        $invoice_service->updateInvoice();

        $invoice_service->setCuf($invoice_service->getResponse()['cuf']);

        // $invoice = $invoice_service->getInvoiceByAckTicket();
        $invoice = $invoice_service->getInvoiceByCuf();

        $this->saveState($invoice['estado'])->saveCuf($invoice_service->getResponse()['cuf'])->saveEmisionDate($invoice['fechaEmision'])->save();

    }

    public function deletePdf()
    {
        $this->invoice_origin()->service()->deletePdf();
    }

    public function invoice_origin()
    {
        // $hashid = new Hashids(config('ninja.hash_salt'), 10);

        // $id_origin_decode = $hashid->decode($this->id_origin)[0];

        return \App\Models\Invoice::withTrashed()->find($this->id_origin);

    }

    public function getCostosGastosNacionalesChangedAttribute()
    {
        return [
            [
                "campo" => "Gasto Transporte",
                "valor" => $this->costosGastosNacionales['gastoTransporte']
            ],
            [
                "campo" => "Gasto de Seguro",
                "valor" => $this->costosGastosNacionales['gastoSeguro']
            ]
        ];

    }
    public function getCostosGastosInternacionalesChangedAttribute()
    {
        return [
            [
                "campo" => "Gasto Transporte",
                "valor" => $this->costosGastosInternacionales['gastoTransporte']
            ],
            [
                "campo" => "Gasto de Seguro",
                "valor" => $this->costosGastosInternacionales['gastoSeguro']
            ]
        ];

    }
    public function getFacturaOrigin(){
        return self::where('id', $this->factura_original_id)->first();
    }
}
