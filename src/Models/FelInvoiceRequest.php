<?php

namespace EmizorIpx\ClientFel\Models;

use EmizorIpx\ClientFel\Exceptions\ClientFelException;
use EmizorIpx\ClientFel\Services\Invoices\Invoices;
use EmizorIpx\ClientFel\Traits\DecodeHashIds;
use EmizorIpx\ClientFel\Utils\TypeDocuments;
use EmizorIpx\PrepagoBags\Exceptions\PrepagoBagsException;
use EmizorIpx\PrepagoBags\Services\AccountPrepagoBagService;
use Hashids\Hashids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class FelInvoiceRequest extends Model
{
    use DecodeHashIds;
    protected $table = "fel_invoice_requests";

    protected $guarded = [];


    protected $cast =[
        'detalles' => 'array'
    ];

    public function getDetallesAttribute()
    {
       return json_decode($this->attributes['detalles'],true);
    }

    public function saveCuf($value) 
    {
        $this->cuf = $value;
        $this->save();
    }

    public static function findByIdOrigin($id_origin)
    {
        $hashid = new Hashids(config('ninja.hash_salt'), 10);

        $id_origin_decode = $hashid->decode($id_origin)[0];
        
        return self::whereIdOrigin($id_origin_decode)->first();
    }

    public static function getByCompanyId($company_id)
    {
        return self::where('company_id', $company_id)->get();
    }

    public function saveState($value){
        $this->estado = $value;
        $this->save();
    }

    public function sendInvoiceToFel($access_token){

        try {

            $prepagoBagService = new AccountPrepagoBagService();

            
            $prepagoBagService->controlPrepagoBag($this->company_id);
            
        } catch (PrepagoBagsException $ex) {
            Log::debug('Fel Error');
            throw new ClientFelException($ex->getMessage());
        }
        
        $invoice_service = new Invoices;

        $invoice_service->setAccessToken($access_token);

        $invoice_service->setBranchNumber(0);

        $invoice_service->buildData($this);

        $invoice_service->setTypeDocument(TypeDocuments::COMPRA_VENTA);

        $invoice_service->sendToFel();

        $invoice_service->setCuf($invoice_service->getResponse()['cuf']);
        
        $invoice = $invoice_service->getInvoiceByCuf();
        
        $this->saveState($invoice['estado']);

        $this->saveCuf($invoice_service->getResponse()['cuf']);


    }
}
