<?php

namespace EmizorIpx\ClientFel\Repository;

use App\Models\RecurringInvoice;
use EmizorIpx\ClientFel\Builders\FelInvoiceBuilder;
use EmizorIpx\ClientFel\Http\Resources\InvoiceResource;
use EmizorIpx\ClientFel\Models\FelClient;
use EmizorIpx\ClientFel\Models\FelClientToken;
use EmizorIpx\ClientFel\Models\FelInvoiceRequest;
use EmizorIpx\ClientFel\Repository\Interfaces\RepoInterface;
use EmizorIpx\ClientFel\Utils\Documents;
use EmizorIpx\ClientFel\Utils\TypeDocumentSector;
use EmizorIpx\PrepagoBags\Models\AccountPrepagoBags;
use Exception;
use Hashids\Hashids;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class FelInvoiceRequestRepository extends BaseRepository implements RepoInterface
{

    public function create($fel_data, $model)
    {
        \Log::debug("create !!!!! ");
        bitacora_info("FelInvoiceRequestRepository:create", json_encode($fel_data));
        
        try {
           
            $this->processInput($fel_data, $model);
            \Log::debug("PARA EL PROCESS INPUT CREATE");
        } catch (Exception $ex) {
            bitacora_error("FelInvoiceRequestRepository:create", "File: " . $ex->getFile() . " Line: " . $ex->getLine() . "Message: " . $ex->getMessage());
        }
    }

    public function update($fel_data, $model)
    {
        \Log::debug("update !!!!! ");
        bitacora_info("FelInvoiceRequestRepository:update", json_encode($fel_data));

        try {
            if (request()->has('felData')) {

                $this->processInput($fel_data, $model, true);
                \Log::debug("PARA EL PROCESS INPUT UPDATE");
            }

        } catch (Exception $ex) {
            bitacora_error("FelInvoiceRequestRepository:update", $ex->getMessage());
        }
    }
    public function delete($model)
    {
        bitacora_info("FelInvoiceRequestRepository:delete", "");

        try {
            // This will only process PREFACTURAS
            $invoice_request = FelInvoiceRequest::where( function( $query ) use ( $model ) {
                
                        if( $model instanceof RecurringInvoice ) {
                            \Log::debug("Is Recurring Invoices >>>>>>>>>>>>>> ");
                            return $query->where('recurring_id_origin', $model->id);

                        }
                        
                        return $query->where('id_origin', $model->id);

                    })->whereNull('cuf')->first();


            if (!is_null($invoice_request)) {
                $invoice_request->delete();
            }

        } catch (Exception $ex) {
            bitacora_error("FelInvoiceRepository:delete", $ex->getMessage());
        }
    }

    public function processInput( $fel_data, $model, $update = false)
    {
        $this->setEntity('invoice');
        $this->parseFelData($fel_data);

        if (is_null($model)) {
            bitacora_error("FelInvoiceRepository:PROCESS model","MODEL INVOICE IS NULL");
            return ;
        }

        $client = FelClient::where('id_origin', $model->client_id)->first();

        if (request()->has('name')) {
            $client->business_name = request('name');
            $client->document_number = request('id_number');
            $client->type_document_id = request('type_document_id');
        }
        
        $source_data = [
            'model' => $model,
            'fel_data_parsed' => $this->fel_data_parsed,
            'client' => $client,
            'user' => $model->user,
            'company' => $model->company->company_detail,
            'update' => $update
        ];

        try{

            //this parte should have number of type document , for example 1 : PLANILLA
            $typeDocument = $this->fel_data_parsed['typeDocument'];

            // this part should have the number of type document sector, for example: 1 : FACTURA COMPRA-VENTA
            $code =  $this->fel_data_parsed['type_document_sector_id'];

            // this an instance of generic builder
            $builder = new FelInvoiceBuilder;

            if ($typeDocument == 0) {
        
                // get instance builder by typde document sector, as default should result in CompraVentaBuilder
                $instance = TypeDocumentSector::getInstanceByCode($code);
            } else {

                // get instance builder by typde document, example: Planilla
                $instance = Documents::getInstanceByName($typeDocument);
            }

            //process input if saved or update 
            $builder->make(new $instance($source_data));

        }catch (Exception $e) {

            \Log::emergency("File: " . $e->getFile() . " Line: " . $e->getLine() . " Message: " . $e->getMessage());
        }
        

    }

    public static function completeDataRequest($data, $company_id){
        
        $hashid = new Hashids(config('ninja.hash_salt'), 10);

        $line_items = [];

        foreach($data['line_items'] as $item){
            $product_id_decode = $hashid->decode($item['product_id']);
            $product = DB::table('products')->where('id', $product_id_decode)->first();
            $product_sync = DB::table('fel_sync_products')->where('id_origin', $product_id_decode)->first();
            
            $item_array = array_merge($item, [
                'product_key' => $product->product_key,
                'notes' => $product->notes,
                'codigo_producto' => $product_sync->codigo_producto
            ]);
            
            array_push($line_items, $item_array);
            
        }
        $data['line_items'] = $line_items;
        $settings = AccountPrepagoBags::where('company_id', $company_id)->first()->settings;
        

        if ( !empty($settings) ) {

            $settings_array = json_decode( $settings); 
            
            foreach($settings_array as $settings_change) {
                
                if ($settings_change->codigo == "1") {
                   return array_merge($data, [
                        'felData' => [
                            "codigoActividad" => $settings_change->activity_id,
                            "codigoLeyenda" => $settings_change->caption_id,
                            "codigoMetodoPago" => $settings_change->payment_method_id
                        ]
                    ]);
                } 
            }

            
        }
        return $data;
    }
    public static function completeDataInvoiceRecurringRequest($invoice)
    {
        
        $fel_invoice = FelInvoiceRequest::where('recurring_id_origin', $invoice->recurring_id)->first();
        \Log::debug("invoice resources completing data : " . json_encode(new InvoiceResource($fel_invoice)));
        //Fix, using same number of recurring invoice
        $fel_invoice->numeroFactura = 0;

        $extras = $fel_invoice->getExtras();
        $extras->facturaTicket = Str::uuid();

        $fel_invoice->extras = $extras;

        return [
            'felData' => new InvoiceResource($fel_invoice)
        ];

    }
}
