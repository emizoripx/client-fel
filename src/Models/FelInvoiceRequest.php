<?php

namespace EmizorIpx\ClientFel\Models;

use Carbon\Carbon;
use EmizorIpx\ClientFel\Exceptions\ClientFelException;
use EmizorIpx\ClientFel\Services\Invoices\Invoices;
use EmizorIpx\ClientFel\Traits\DecodeHashIds;
use EmizorIpx\ClientFel\Traits\GetCredentialsTrait;
use EmizorIpx\ClientFel\Traits\GetInvoiceStateTrait;
use EmizorIpx\ClientFel\Traits\InvoiceUpdateDateTrait;
use EmizorIpx\ClientFel\Utils\TypeDocuments;
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
        'otrosDatos' => 'array'
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
    public function saveUrlSin($value) 
    {
        $this->urlSin = $value;
        return $this;
    }
    public function getUrlSin() 
    {
        $url = config('clientfel.host_sin').$this->urlSin;
        \Log::debug("Url Sin: ".$url);
        return $url;
    }

    public static function findByIdOrigin($id_origin)
    {
        $hashid = new Hashids(config('ninja.hash_salt'), 10);

        $id_origin_decode = $hashid->decode($id_origin)[0];
        \Log::debug('id origin '.$id_origin_decode);
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

    public function saveEmisionDate(){
        $this->fechaEmision = substr(Carbon::now()->format('Y-m-d\TH:i:s.u'), 0, -3);
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
    
    /**
     * Get the prepagoAccount instance
     *
     * 
     */
    public function prepagoAccount()
    {
        $hashid = new Hashids(config('ninja.hash_salt'), 10);

        $company_id_decode = $hashid->decode($this->company_id)[0];
        $accountDetails = AccountPrepagoBags::where('company_id', $company_id_decode)->first();
        return $accountDetails;
    }

    public function sendInvoiceToFel(){

        try {

            $prepagoBagService = new AccountPrepagoBagService();

            
            $prepagoBagService->controlPrepagoBag($this->company_id);
            
        } catch (PrepagoBagsException $ex) {
            Log::debug('Fel Error');
            throw new ClientFelException($ex->getMessage());
        }
        
        Log::debug('Credentials');
        Log::debug($this->access_token);
        Log::debug($this->host);
        
        $invoice_service = new Invoices($this->host);

        $invoice_service->setAccessToken($this->access_token);


        $invoice_service->setBranchNumber(0);

        $invoice_service->buildData($this);

        $invoice_service->setTypeDocument(TypeDocuments::COMPRA_VENTA);

        $invoice_service->sendToFel();

        $invoice_service->setCuf($invoice_service->getResponse()['cuf']);
        
        $invoice = $invoice_service->getInvoiceByCuf();
        
        $this->saveState($invoice['estado'])->saveCuf($invoice_service->getResponse()['cuf'])->saveUrlSin($invoice['urlSin'])->saveEmisionDate()->save();

        // $this->setCompanyId();
        Log::debug('RETORNO MODELO');
        Log::debug($this);
        Log::debug('Restar numero de Facturas');
        Log::debug($this->prepagoAccount());
        
        $account = $this->prepagoAccount();
        if(!$account->checkIsPostpago()){
            $account->reduceNumberInvoice()->save();
        }
    }


    public function sendRevocateInvoiceToFel($codigoMotivoAnulacion){
        $invoice_service = new Invoices($this->host);

        $invoice_service->setAccessToken($this->access_token);
        $invoice_service->setCuf($this->cuf);
        $invoice_service->setRevocationReasonCode($codigoMotivoAnulacion);

        $invoice_service->revocateInvoice();

        $invoice = $invoice_service->getInvoicebyCuf();

        $this->saveState($invoice['estado'])->saveRevocationReasonCode($codigoMotivoAnulacion)->save();
    }

    public function sendReversionRevocateInvoiceToFel(){
        $invoice_service = new Invoices($this->host);

        $invoice_service->setAccessToken($this->access_token);
        $invoice_service->setCuf($this->cuf);

        $invoice_service->reversionRevocateInvoice();

        $invoice = $invoice_service->getInvoiceByCuf();

        $this->saveState($invoice['estado'])->save();
    }

    public function sendUpdateInvoiceToFel(){
        $invoice_service = new Invoices($this->host);

        $invoice_service->setAccessToken($this->access_token);
        $invoice_service->setCuf($this->cuf);
        $invoice_service->setBranchNumber(0);

        $invoice_service->buildData($this);
        $invoice_service->setTypeDocument(TypeDocuments::COMPRA_VENTA);

        $invoice_service->updateInvoice();

        $invoice_service->setCuf($invoice_service->getResponse()['cuf']);

        $invoice = $invoice_service->getInvoiceByCuf();

        $this->saveState($invoice['estado'])->saveCuf($invoice_service->getResponse()['cuf'])->saveEmisionDate()->save();

    }
}
