<?php

namespace EmizorIpx\ClientFel\Models;

use App\Models\Invoice;
use Database\Factories\FelInvoiceRequestFactory;
use EmizorIpx\ClientFel\Exceptions\ClientFelException;
use EmizorIpx\ClientFel\Services\Invoices\Invoices;
use EmizorIpx\ClientFel\Traits\DecodeHashIds;
use EmizorIpx\ClientFel\Traits\GetCredentialsTrait;
use EmizorIpx\ClientFel\Traits\GetInvoiceStateTrait;
use EmizorIpx\ClientFel\Traits\InvoiceUpdateDateTrait;
use EmizorIpx\PrepagoBags\Exceptions\PrepagoBagsException;
use EmizorIpx\PrepagoBags\Models\AccountPrepagoBags;
use Exception;
use Hashids\Hashids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use stdClass;
use Carbon\Carbon;
use Throwable;

class FelInvoiceRequest extends Model
{
    use DecodeHashIds;
    use GetCredentialsTrait;
    use GetInvoiceStateTrait;
    use InvoiceUpdateDateTrait;

    use SoftDeletes;
    use HasFactory;

    protected $table = "fel_invoice_requests";

    protected $guarded = [];


    protected $casts =[
        'detalles' => 'array',
        'external_invoice_data' => 'array',
        'otrosDatos' => 'array',
        'costosGastosNacionales' => 'array',
        'costosGastosInternacionales' => 'array',
        'data_specific_by_sector' => 'array',
    ];

    protected $access_token;
    protected $host;


    protected static function boot()
    {
        parent::boot();
        static::creating(function ($query) {
            if( $query->typeDocument == 0 ){
                $next_number = self::nextNumber($query->company_id);
                $query->prefactura_number = $next_number;
            }

            if ($query->typeDocument == 1) {
                $next_number = self::nextNumber($query->company_id,'planilla');
                $query->document_number = $next_number;
            }
        });
    }

    public static function nextNumber($company_id, $document = "prefactura")
    {
        
        $hashid = new Hashids(config('ninja.hash_salt'), 10);
        $company_id = $hashid->decode($company_id);

        $data = AccountPrepagoBags::whereCompanyId($company_id)->select('id',$document.'_number_counter')->first();

        
        if ($data!=null) {
            $data->increment($document.'_number_counter');
            \Log::debug("COMPANY=". $data->id. " >>>>>>>>>>>>>>>>>> NEXT-NUMBER-". strtoupper($document) ." = ". $data->prefactura_number_counter );    
            return $data->{$document."_number_counter"};
        }
        \Log::debug("PREFACTURA NEXT-NUMBER FROM COMPANY: $company_id >>>>>>>>>>>>>>>>>> 1" );
        return 1;
    }

    public function getNumeroFacturaAttribute()
    {
        if ($this->attributes['typeDocument'] == 1) {
            return "Planilla " . $this->attributes['document_number'];
        }

        if ( $this->attributes['numeroFactura'] == 0 ) {
            return "Pre-factura " . $this->attributes['prefactura_number'];
        }
        return $this->attributes['numeroFactura'];
    }

    protected static function newFactory(){
        return FelInvoiceRequestFactory::new();
    }

    public function getDetallesAttribute()
    {
        $result = json_decode($this->attributes['detalles'], true);
        return is_array($result) ?  $result : $result['original'];
    }

    public function saveCuf($value) 
    {
        \Log::debug("Saving CUF......");
        if(!is_null($value)){
            
            $this->cuf = $value;

            $this->save();
        }

        return $this;
    }
    public function saveAckTicket($value) 
    {
        \Log::debug("Saving AckTicket......");
        if(! is_null($value)){
            $this->ack_ticket = $value;
            $this->save();
        }
        \Log::debug("Save AckTicket......");
    }
    public function saveUrlSin($value) 
    {
        if(!is_null($value)){
            $this->urlSin = $value;
        }
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

    public function getFacturaOriginalIdHashedAttribute()
    {
        if ( is_null($this->factura_original_id)) 
            return null;
        
        $hashid = new Hashids(config('ninja.hash_salt'), 10);

        return $hashid->encode($this->factura_original_id);
        
    }


    public static function getByCompanyId($company_id)
    {
        return self::withTrashed()->where('company_id', $company_id)->get();
    }

    public function saveState($value){
        if(!is_null($value)){

            $this->estado = $this->getInvoiceState($value);
        }
        return $this;
    }
    public function savePackageId($value){
        $this->package_id = $value;
        return $this;
    }
    public function saveIndexPackage($value){
        $this->index_package = $value;
        return $this;
    }
    public function saveUuidPackage($value){
        $this->uuid_package = $value;
        return $this;
    }

    public function saveEmisionDate($fechaEmision){
        $this->fechaEmision = $fechaEmision;
        return $this;
    }

    public function saveStatusCode($value){
        if(!is_null($value)){

            $this->codigoEstado = $value;
        }
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
    
    public function saveEmisionType( $value)
    {
        if(! is_null($value)){
            $this->emission_type = 'Fuera de línea';

            if (is_array($value)){
                if ($value['codigo'] == 1) {
                    $this->emission_type = 'En línea';
                }}
            else{
                if ($value == 1){
                    $this->emission_type = 'En línea';
                }}
        }
        return $this;
    }

    public function getLeyendaEmissionType(){
        if($this->emission_type == "Fuera de línea"){
            return FelCaption::PARAMETRIC_OFFLINE;
        }
        return FelCaption::PARAMETRIC_ONLINE;
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
        
        if( !is_null($value)){
            $this->direccion = $value;
        }

        return $this;
    }
    public function saveXmlUrl($value){
        
        if( !is_null($value)){
            $this->xml_url = $value;
        }

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
        \Log::debug("################################################## FLUJO ============================================ ENVIANDO AL FEL LA FACTURA");
        try{
            $invoice_service->sendToFel();
        } catch (Exception $ex) {
            throw new ClientFelException($ex->getMessage());
        }
        $res = $invoice_service->getResponse();
        \Log::debug("RESPONSE FEL ===========> " . json_encode( $res ));
        
        \DB::table("fel_invoice_requests")
            ->whereId($this->id)
            ->update(['cuf'=> $res['cuf'],
                     'urlSin' => $res['urlSin'], 
                     'ack_ticket' => $res['ack_ticket'],
                     'emission_type' => $res['emission_type_code'] == 2 ? "Fuera de línea" : "En línea", 
                     'fechaEmision' => Carbon::parse($res['fechaEmision'])->toDateTimeString() 
                    ]);
        \Log::debug("################################################## FLUJO ============================================ RESPUESTA GUARADA EN LA BASE DE DATOS");
        \DB::table('invoices')->whereId($this->id_origin)->update([ 'date' => Carbon::parse($res['fechaEmision'])->toDateString()]);
        $this->invoiceDateUpdatedAt();
        $account = $this->felCompany();
        if(!$account->checkIsPostpago()){
            $detailCompanyDocumentSector->reduceNumberInvoice()->setCounter()->save();
        } else {
            $detailCompanyDocumentSector->setPostpagoCounter()->setCounter()->save();
        }
        \Log::debug("################################################## FLUJO ============================================ FIN ENVIANDO AL FEL LA FACTURA");
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

        $invoice_service->reversionRevocateInvoice();
        // $invoice = $invoice_service->getInvoiceByAckTicket();
        $invoice = $invoice_service->getInvoiceByCuf();

        // $invoice = $invoice_service->getInvoiceByAckTicket();

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

        $this->saveState($invoice['estado'])->saveCuf($invoice_service->getResponse()['cuf'])->saveEmisionDate($invoice['fechaEmision'])->saveUrlSin($invoice['urlSin']?? null)->save();

    }

    public function sendVerifyStatus()
    {
        \Log::debug("LA FACTURA ESTA CON ESTADO : " . $this->codigoEstado);
        // if ( in_array($this->codigoEstado,[908,690,902,904]) ) {
        //     \Log::debug("SALTANDO LA CONSULTA DEL ESTADO POR QUE TIENE EL ESTADO =======================: " . $this->codigoEstado);
        //     return true;
        // }
            
        $invoice_service = new Invoices($this->host);

        $invoice_service->setAccessToken($this->access_token);
        $invoice_service->setAckTicket($this->ack_ticket);

        $response = $invoice_service->getStatusByAckTicket();

        // if ( in_array($response['codigoEstado'],[908,690,902,904, 691, 906]) ) {
        try{

            $this->saveStatusCode($response['codigoEstado']);
            $this->estado = $response['estado'];
            $this->save();

        }catch (Throwable $th) {
            \Log::debug("SEND VERIFY STATUS");
            return [];
        }
        // }
        return $response;
        
    }

    public function deletePdf()
    {
        $this->invoice_origin()->service()->deletePdf();
    }
    public function touchPdf()
    {
        $this->invoice_origin()->service()->touchPdf();
    }

    public function invoice_origin()
    {

        return \App\Models\Invoice::withTrashed()->find($this->id_origin);

    }

    public function setPaisDestinoAttribute($value)
    {

        if ($value >=1 && $value <= 208)
            $this->attributes['paisDestino'] = $value;
        else
            $this->attributes['paisDestino'] = null;
    }

    public function getCostosGastosNacionalesChangedAttribute()
    {
        
        $data =[];
        foreach (self::ensureIterable($this->costosGastosNacionales) as $key => $value) {
            $data[] = ["campo" => $key, "valor"=>$value];
        }
        
        return $data;

    }
    public function getCostosGastosInternacionalesChangedAttribute()
    {
        
        $data = [];
        foreach ( self::ensureIterable($this->costosGastosInternacionales)  as $key => $value) {
            $data[] = ["campo" => $key, "valor" => $value];
        }
        
        return $data;
    }
    public function getFacturaOrigin(){
        return Invoice::where('id', $this->factura_original_id)->first();
    }

    public function getExchangeDescription()
    {
        
        return  \DB::table('fel_currencies')->whereCodigo($this->codigoMoneda)->first()->descripcion;
    }

    public function getFechaEmisionFormated(){
        $date = Carbon::parse($this->fechaEmision,'America/La_Paz')->format("d/m/Y g:i A");
        return  $date;
    }

    public function setNumeroFactura($number)
    {
        // condition to detect if  numeroFactura still doest not have value,
        // check if contains "Pre", this is because, in an above method there is a mutator that changes value in case is 0
        if ($this->numeroFactura == 0 ||  strpos( $this->numeroFactura,"Pre") === 0) {
            $this->numeroFactura = $number;
            $this->save();
        }
    }

    public function getExtras()
    {

        $extras_aux = self::ensureIterable($this->extras);

        $aux= new \stdClass;
        foreach ($extras_aux as $key => $value) {
            $aux->{$key} = is_null($value) ? "" : $value;
        }
        return $aux;
    }

    public function getVariableExtra($extra)
    {
        $extras = self::ensureIterable($this->extras);

        if (empty($extras)) 
            return "";

        if ( is_array($extras) ) 
            return $extras[$extra];
        
        if ( is_object($extras) && isset($extras->{$extra}) )
            return $extras->{$extra};

        return "";

    }

    public static function ensureIterable($var)
    {
        if (is_array($var) || is_object($var))
            return $var;

        if (is_null($var))
            return [];

        $extras_aux = json_decode($var);

        if (!is_array($extras_aux) && !is_object($extras_aux))
            return [];

        return $extras_aux;
    }

    public static function forceToArrayExtras($var)
    {
        $arr = [];
        if ( is_array($var) )
            return $var;

        if (is_object($var)) {
            foreach ($var as $key => $value) {
                $arr[$key] = $value;
            }
        }
            
        return $arr;
    }

    public function getBranchByCode()
    {
        $hashid = new Hashids(config('ninja.hash_salt'), 10);
        \Log::debug( " Get Branch Code - Company ID:  ". $hashid->decode($this->company_id)[0]);
        return FelBranch::whereCompanyId($hashid->decode($this->company_id)[0])->whereCodigo(strval($this->codigoSucursal))->first();
    }
    public function getLeyenda()
    {
        return FelCaption::getCaptionDescription($this->codigoLeyenda);
    }

     public function originalExternalInvoice()
    {
        \Log::debug("Get Factura Origin External");

        if ($this->facturaExterna == 1) {

            $hashid = new Hashids(config('ninja.hash_salt'), 10);

            $newInvoice = new Invoice();

            $new = new FelInvoiceRequest();
            $new->cuf = $this->numeroAutorizacionCuf;
            $new->company_id = $hashid->encode($this->company_id);
            $new->numeroFactura = $this->external_invoice_data['numeroFacturaOriginal'];
            $new->fechaEmision = $this->external_invoice_data['fechaEmisionOriginal'];
            $new->montoTotal = $this->external_invoice_data['montoTotalOriginal'] ;
            $new->codigoControl = isset($this->external_invoice_data['codigoControl']) ?$this->external_invoice_data['codigoControl']:"";
            $new->typeDocument = 2;
            $new->codigoSucursal = $this->codigoSucursal;
            $new->detalles = (json_decode($this->attributes['detalles'], true))['original'];

            $newInvoice->fel_invoice = $new;

            return $newInvoice;
        }
        return [];
    }

    public function setEmittedByUser()
    {
        $this->emitted_by = auth()->user() ? auth()->user()->id : null;
        $this->save();
    }

    public function setRevocatedByUser()
    {
        $this->revocated_by = auth()->user() ? auth()->user()->id : null;
        $this->save();
    }

}
