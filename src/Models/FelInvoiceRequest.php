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
use EmizorIpx\ClientFel\Jobs\BiocenterStatusNotification;
use EmizorIpx\ClientFel\Jobs\GetInvoiceStatus;
use EmizorIpx\ClientFel\Utils\InvoiceStates;
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
            if( $query->typeDocument == 0 && (request()->input('should_emit') == 'false' || ! request()->has('should_emit')) ){
                $next_number = self::nextNumber($query->company_id);
                $query->prefactura_number = $next_number;
            }

            if ($query->typeDocument == 2 || $query->typeDocument == 3) {
                $next_number = empty($query->factura_original_id) ? self::nextNumber($query->company_id,'order') : $query->document_number;
                $query->document_number = $next_number;
            }

            if ($query->typeDocument == 1) {
                $next_number = self::nextNumber($query->company_id,'planilla');
                $query->document_number = $next_number;
            }
        });
    }

    public static function nextNumber($company_id, $document = "prefactura")
    {
        
        $data_number_document = 1;
        $hashid = new Hashids(config('ninja.hash_salt'), 10);
        $company_id = $hashid->decode($company_id);

        \DB::transaction(function () use ($company_id, &$data_number_document, $document) {
            // Some database updates
            
            $data = AccountPrepagoBags::whereCompanyId($company_id)->select('id',$document.'_number_counter')->lockForUpdate()->first();
            
            if ($data!=null) {
                $data_number_document = $data->{$document . '_number_counter'};
            
                \DB::table('fel_company')->where('id',$data->id)->update([
                    $document . '_number_counter' => $data_number_document + 1
                ]);
                \Log::debug("COMPANY=". $data->id. " >>>>>>>>>>>>>>>>>> NEXT-NUMBER-". strtoupper($document) ." = ". $data->{$document . '_number_counter'} );    
            } else {

                \Log::debug("$document NEXT-NUMBER FROM COMPANY: $company_id >>>>>>>>>>>>>>>>>> 1" );
                
            }
            return true;
        });
      

        return $data_number_document;
        
    }

    public function getNumeroFacturaAttribute()
    {
        if ($this->attributes['typeDocument'] == 3) {
            return "Orden " . $this->attributes['document_number'];
        }

        if ($this->attributes['typeDocument'] == 1) {
            return "Planilla " . $this->attributes['document_number'];
        }

        if ( $this->attributes['numeroFactura'] == 0 && !is_null($this->attributes['prefactura_number']) ) {
            return "Pre-factura " . $this->attributes['prefactura_number'];
        }
        return $this->attributes['numeroFactura'];
    }

    public function getComplementoAttribute()
    {
        if ($this->attributes['codigoTipoDocumentoIdentidad'] == 5) {
            return null;
        }

        return $this->attributes['complemento'];
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


        \Log::debug("Verificando factura Ticket   ", [$this->getVariableExtra("facturaTicket")]);

        if ($this->getVariableExtra("facturaTicket") == "") {
            \Log::debug("generear factura Ticket");
            $bytes = random_bytes(20);
            $new_extras = $this->getExtras();
            $new_extras->facturaTicket = bin2hex($bytes);
            $this->extras = json_encode($new_extras);
        }

        if (is_null($this->factura_ticket)) {
            $this->factura_ticket = $this->getVariableExtra("facturaTicket");
            $this->save();
        }
        try{
            $invoice_service = new Invoices($this->host, $this->access_token);
            $invoice_service->buildData($this);
            $invoice_service->sendToFel();

            if ($invoice_service->isSuccessful()) {
                $invoice_service->getDetails($this->factura_ticket);

                if ($invoice_service->isSuccessful()) { 

                    $result = (array)$invoice_service->getResponse();
                    \DB::table("fel_invoice_requests")
                        ->whereId($this->id)
                        ->update([
                            'cuf' => $result['cuf'],
                            'urlSin' => isset($result['urlSin']) ? $result['urlSin'] . "&t=2" : "",
                            'xml_url' => $result['xml_url'],
                            'ack_ticket' => isset($result['ack_ticket']) ? $result['ack_ticket'] : null,
                            'package_id' => $result['package_id'],
                            'uuid_package' => $result['uuid_package'],
                            'index_package' => $result['index_package'],
                            'emission_type' => isset($result['tipoEmision']['codigo']) ? ( $result['tipoEmision']['codigo'] == 2 ? "Fuera de línea" : "En línea") : "En línea",
                            'fechaEmision' => Carbon::parse($result['fechaEmision'])->toDateTimeString(),
                            'codigoEstado' => $result['codigoEstado'],
                            'errores' => $result['errores'],
                        ]);

                    \DB::table('invoices')->whereId($this->id_origin)->update(['date' => Carbon::parse($result['fechaEmision'])->toDateString()]);
                    $this->invoiceDateUpdatedAt();

                // $account = $this->felCompany();
                // if (!$account->checkIsPostpago()) {
                //     $detailCompanyDocumentSector->reduceNumberInvoice()->setCounter()->save();
                // } else {
                //     $detailCompanyDocumentSector->setPostpagoCounter()->setCounter()->save();
                // }
                }else {
                    \DB::table("fel_invoice_requests")
                    ->whereId($this->id)
                    ->update([
                        'errores' => $invoice_service->getErrors()
                    ]);
                }

                GetInvoiceStatus::dispatch( $this, InvoiceStates::EMIT_ACTION )->delay( now()->addSeconds( 5 ) );

            } else {

                \DB::table("fel_invoice_requests")
                    ->whereId($this->id)
                    ->update([
                        'errores' => $invoice_service->getErrors(),
                        'codigoEstado' => 902,
                        'estado' => "RECHAZADA",
                    ]);
            }

        } catch (Exception $ex) {
            info("ERROR EXCEPTION FELINVOICEREQUEST >>>>>>> message : " . $ex->getMessage() . " File: " . $ex->getFile() . " Line: " . $ex->getLine() );
            \DB::table("fel_invoice_requests")
                ->whereId($this->id)
                ->update([
                    'errores' => ['code'=>9999, "description"=>"Error inesperado, consulte con su administrador"]
                ]);
            bitacora_error("FelInvoiceRequest:sentofel", $ex->getMessage());
        }

    }

    public function sendRevocateInvoiceToFel($codigoMotivoAnulacion){
        $invoice_service = new Invoices($this->host, $this->access_token);

        $invoice_service->setRevocationReasonCode($codigoMotivoAnulacion);

        $invoice_service->revocateInvoice($this->factura_ticket);

        if ($invoice_service->isSuccessful()) {
            $invoice_service->getDetails($this->factura_ticket);

            if ($invoice_service->isSuccessful()) {
                $result = (array)$invoice_service->getResponse();
                \DB::table("fel_invoice_requests")
                    ->whereId($this->id)
                    ->update([
                        'cuf' => $result['cuf'],
                        'urlSin' => isset($result['urlSin']) ? $result['urlSin'] . "&t=2" : "",
                        'xml_url' => $result['xml_url'],
                        'ack_ticket' => isset($result['ack_ticket']) ? $result['ack_ticket'] : null,
                        'package_id' => $result['package_id'],
                        'uuid_package' => $result['uuid_package'],
                        'index_package' => $result['index_package'],
                        'emission_type' => isset($result['tipoEmision']['codigo']) ? ($result['tipoEmision']['codigo'] == 2 ? "Fuera de línea" : "En línea") : "En línea",
                        'fechaEmision' => Carbon::parse($result['fechaEmision'])->toDateTimeString(),
                        'codigoEstado' => $result['codigoEstado'],
                        'errores' => $result['errores'],
                    ]);

                \DB::table('invoices')->whereId($this->id_origin)->update(['date' => Carbon::parse($result['fechaEmision'])->toDateString()]);
                $this->invoiceDateUpdatedAt();
            }
            
            GetInvoiceStatus::dispatch( $this, InvoiceStates::REVOCATE_ACTION )->delay( now()->addSeconds( 5 ) );
        }else {
            // there was no successful
            $errors = $invoice_service->getErrors();
            throw new ClientFelException($errors);
        }

    }


    public function sendRevocateReversionInvoiceToFel(){
        $invoice_service = new Invoices($this->host, $this->access_token);

        $invoice_service->reversionRevocateInvoice($this->factura_ticket);

        if ($invoice_service->isSuccessful()) {
            $invoice_service->getDetails($this->factura_ticket);

            if ($invoice_service->isSuccessful()) {
                $result = (array)$invoice_service->getResponse();
                \DB::table("fel_invoice_requests")
                    ->whereId($this->id)
                    ->update([
                        'cuf' => $result['cuf'],
                        'urlSin' => isset($result['urlSin']) ? $result['urlSin'] . "&t=2" : "",
                        'xml_url' => $result['xml_url'],
                        'ack_ticket' => isset($result['ack_ticket']) ? $result['ack_ticket'] : null,
                        'package_id' => $result['package_id'],
                        'uuid_package' => $result['uuid_package'],
                        'index_package' => $result['index_package'],
                        'emission_type' => isset($result['tipoEmision']['codigo']) ? ($result['tipoEmision']['codigo'] == 2 ? "Fuera de línea" : "En línea") : "En línea",
                        'fechaEmision' => Carbon::parse($result['fechaEmision'])->toDateTimeString(),
                        'codigoEstado' => $result['codigoEstado'],
                        'errores' => $result['errores'],
                    ]);

                \DB::table('invoices')->whereId($this->id_origin)->update(['date' => Carbon::parse($result['fechaEmision'])->toDateString()]);
                $this->invoiceDateUpdatedAt();
            }
            
        }
        // GetInvoiceStatus::dispatch( $this, InvoiceStates::REVERSION_REVOCATE_ACTION )->delay( now()->addSeconds( 5 ) );

    }


    public function sendUpdateInvoiceToFel(){

        $invoice_service = new Invoices($this->host, $this->access_token);
        $invoice_service->setBranchNumber($this->codigoSucursal);

        \Log::debug("VERIFICANDO factura TIcket en update   ", [$this->getVariableExtra("facturaTicket")]);

        if ($this->getVariableExtra("facturaTicket") == "") {
            \Log::debug("generear factira Ticket");
            $bytes = random_bytes(20);
            $new_extras = $this->getExtras();
            $new_extras->facturaTicket = bin2hex($bytes);
            $this->extras = json_encode($new_extras);
        }
        if ( is_null($this->factura_ticket) ) {
            $this->factura_ticket = $this->getVariableExtra("facturaTicket");
            $this->save();
        }
        
        $invoice_service->buildData($this);

        if ($invoice_service->isSuccessful()) {
            $invoice_service->getDetails($this->factura_ticket);

            if ($invoice_service->isSuccessful()) {

                $result = (array)$invoice_service->getResponse();
                \DB::table("fel_invoice_requests")
                ->whereId($this->id)
                    ->update([
                        'cuf' => $result['cuf'],
                        'urlSin' => isset($result['urlSin']) ? $result['urlSin'] . "&t=2" : "",
                        'xml_url' => $result['xml_url'],
                        'ack_ticket' => isset($result['ack_ticket']) ? $result['ack_ticket'] : null,
                        'package_id' => $result['package_id'],
                        'uuid_package' => $result['uuid_package'],
                        'index_package' => $result['index_package'],
                        'emission_type' => isset($result['tipoEmision']['codigo']) ? ($result['tipoEmision']['codigo'] == 2 ? "Fuera de línea" : "En línea") : "En línea",
                        'fechaEmision' => Carbon::parse($result['fechaEmision'])->toDateTimeString(),
                        'codigoEstado' => $result['codigoEstado'],
                        'errores' => $result['errores'],
                    ]);

                \DB::table('invoices')->whereId($this->id_origin)->update(['date' => Carbon::parse($result['fechaEmision'])->toDateString()]);
                $this->invoiceDateUpdatedAt();

                // $account = $this->felCompany();
                // if (!$account->checkIsPostpago()) {
                //     $detailCompanyDocumentSector->reduceNumberInvoice()->setCounter()->save();
                // } else {
                //     $detailCompanyDocumentSector->setPostpagoCounter()->setCounter()->save();
                // }
            } else {
                \DB::table("fel_invoice_requests")
                ->whereId($this->id)
                    ->update([
                        'errores' => $invoice_service->getErrors()
                    ]);
            }
        } else {

            \DB::table("fel_invoice_requests")
            ->whereId($this->id)
                ->update([
                    'errores' => $invoice_service->getErrors()
                ]);
        }
    }

    public function sendVerifyStatus()
    {
        \Log::debug("LA FACTURA ESTA CON ESTADO : " . $this->codigoEstado);

        // if (  in_array($this->codigoEstado,[690, 908]) && is_null($this->revocation_reason_code)  ) {
        //     return true;
        // }
        // if ( in_array($this->codigoEstado,[908,690,902,904]) ) {
        //     \Log::debug("SALTANDO LA CONSULTA DEL ESTADO POR QUE TIENE EL ESTADO =======================: " . $this->codigoEstado);
        //     return true;
        // }
            
        $invoice_service = new Invoices($this->host, $this->access_token);
        
        $invoice_service->getStatus($this->factura_ticket);

        if ($invoice_service->isSuccessful()) {
            // if ( in_array($response['codigoEstado'],[908,690,902,904, 691, 906]) ) {
                $response = $invoice_service->getResponse();
            try {
                $estadoAntiguo = $this->estado;
                $this->saveStatusCode($response['codigoEstado']);
                $this->estado = $response['estado'];
                if (!empty($response) && isset($response['errores'])) {
                    $this->errores = $response['errores'];
                }
                $this->save();


                if ($estadoAntiguo == "ANULACION EN ESPERA") {
                    // REMOVE PDF WHEN REVOCATIO IS WAITIN
                    \Log::debug("\n\n\n\n\n removing PDF cause is waiting\n\n\n");
                    $this->deletePdf();
                }
                if ($estadoAntiguo != $this->estado) {
                    BiocenterStatusNotification::dispatch($this->invoice_origin());
                }

                if (!empty($response) && isset($response['errores'])) {
                    $response['errores'] = json_encode($response['errores']);
                }
                return $response;
            }catch (Throwable $th) {
                \Log::debug("SEND VERIFY STATUS");
                return [];
            }
        }else {
            info("errors in get status   " , $invoice_service->getErrors());
            return ["codigoEstado" => 902,"estado" => $this->estado,"errores" => $this->errores];
        }
        
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

    public function setNumeroFactura($numeroFacturaFromInvoice = null)
    {
        $log_info = request("tstms_small") . "SET-NUMERO-FACTURA " ;

        info($log_info ."input=" . $numeroFacturaFromInvoice);
        // condition to detect if  numeroFactura still doest not have value,
        // check if contains "Pre", this is because, in an above method there is a mutator that changes value in case is 0
        info($log_info . " old numeroFactura =" . $this->numeroFactura);
        if ($this->numeroFactura == 0 ||  strpos( $this->numeroFactura,"Pre") === 0) {

            info($log_info . "CHECKING INVOICE_GENERATOR_NUMBER...");
            $obj = \DB::table('fel_company')->where('company_id', $this->getCompanyIdDecoded())->select('level_invoice_number_generation')->first();
            
            if (empty($obj)) 
                return 1;

            info($log_info .'LEVEL INVOICE NUMBER GENERATION = ' . $obj->level_invoice_number_generation);
            switch ($obj->level_invoice_number_generation) {
                case 0:
                    $numeroFactura = !is_null($numeroFacturaFromInvoice) ? $numeroFacturaFromInvoice : 1;
                    break;
                case 1:
                    $numeroFactura = InvoiceGeneratorNumber::nextNumber($this->getCompanyIdDecoded(), $this->codigoSucursal);
                    break;
                case 2:
                    $numeroFactura = InvoiceGeneratorNumber::nextNumber($this->getCompanyIdDecoded(), $this->codigoSucursal, $this->codigoPuntoVenta);
                    break;
                case 3:
                    $numeroFactura = InvoiceGeneratorNumber::nextNumber($this->getCompanyIdDecoded(), $this->codigoSucursal, $this->codigoPuntoVenta, $this->type_document_sector_id);
                    break;
                default:
                    $numeroFactura = 1;
                    break;
            }
            info($log_info . ' save= ' . $numeroFactura);
            $this->numeroFactura = $numeroFactura;
            $this->save();
        }else {
            logger()->error( $log_info . " !! NOT SET INVOICE NUMBER !! ");
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

    public function getCompanyIdDecoded()
    {
        $hashid = new Hashids(config('ninja.hash_salt'), 10);
        return $hashid->decode($this->company_id)[0];
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

    public function isEmitted()
    {
        return !is_null($this->cuf); // if there is no cuf was not emitted yet
    }

    public function savePolicyCnc()
    {
        if ( $this->getVariableExtra('poliza') != "" ) {
            \DB::table('policies_invoices')
            ->insert(
                [
                    'policy_code'=> $this->getVariableExtra('poliza'),
                    'fel_invoice_request_id' =>$this->id,
                    'created_at' => \Carbon\Carbon::now(),
                    'updated_at' => \Carbon\Carbon::now()
                ]
            );
        }        
    }


    public function getEmailAgency()
    {
        $id_agencia = $this->getVariableExtra('id_agencia');

        if (!is_null($id_agencia)) {
            $agency = \DB::table("agencies")->find($id_agencia);
            if ($agency && isset($agency->email)) {
                return $agency->email;
            }
        }
        return null;
    }

    public static function getStatusText($status)
    {
        $common_class = "inline-flex items-center px-3 py-2 rounded-full gap-x-2 text-red-500 text-sm font-normal ";
        
        $valid ='<div class="'.$common_class. ' text-emerald-500 bg-emerald-100/60 dark:bg-gray-800"><h2>Válido</h2> </div>';
        $rejected = '<div class="' . $common_class . ' text-red-500 bg-red-100/60 dark:bg-gray-800"><h2>Rechazado</h2> </div>';
        $pending = '<div class="' . $common_class . ' text-orange-500 bg-orange-100/60"><h2></h2> </div>';
        $sn = '<div class="' . $common_class . ' "><h2></h2> </div>';
                    

        if ($status == 902) {
            return $rejected;
        }

        if ($status == "") {
            return $sn;
        }

        if ($status == 690) {
            return $valid;
        }
        
    }

}
