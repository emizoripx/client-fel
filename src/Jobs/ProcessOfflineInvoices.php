<?php

namespace EmizorIpx\ClientFel\Jobs;

use EmizorIpx\ClientFel\Exceptions\ClientFelException;
use EmizorIpx\ClientFel\Models\FelBranch;
use EmizorIpx\ClientFel\Models\FelClientToken;
use EmizorIpx\ClientFel\Models\FelInvoiceRequest;
use EmizorIpx\ClientFel\Models\FelOfflineEvent;
use EmizorIpx\ClientFel\Models\FelOfflinePackage;
use EmizorIpx\ClientFel\Models\FelPOS;
use EmizorIpx\ClientFel\Services\OfflineEvent\FelOfflineEventService;
use EmizorIpx\ClientFel\Services\OfflineEvent\Resources\TypeDocumentResource;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use GuzzleHttp\Psr7;

class ProcessOfflineInvoices implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $offline_event_id;

    protected $company_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( $offline_event_id, $company_id )
    {

        $this->offline_event_id = $offline_event_id;

        $this->company_id = $company_id;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            
            $offline_event = FelOfflineEvent::where('id', $this->offline_event_id)->where('company_id', $this->company_id)->where('state', FelOfflineEvent::PENDING_STATE)->first();

            if( !$offline_event ){
                \Log::debug(" No se encontro un evento Pendiente con el ID: " . $this->offline_event_id );

                throw new Exception(" No se encontro un evento Pendiente con el ID: " . $this->offline_event_id);  
            }

            $package_ids = FelOfflinePackage::where('offline_event_id', $this->offline_event_id)->where('state', FelOfflinePackage::PENDING_STATE)->pluck('type_document_code','id')->toArray();

            \Log::debug("Packages: " . json_encode($package_ids));
            if( count($package_ids) == 0 ) {
                throw new Exception("El evento " . $this->offline_event_id . " no tiene paquetes de facturas");
            }
            $company_token = FelClientToken::where('account_id', $this->company_id)->firstOrFail();

            if( ! $company_token ) {
                throw new Exception("No se encontro credenciales para la compañía:  " . $this->company_id);
            }

            $fel_offline_event_service = new FelOfflineEventService( $company_token->host );

            $fel_offline_event_service->setAccessToken( $company_token->access_token );

            $branch = FelBranch::where('company_id', $this->company_id)->where('id', $offline_event->branch_id)->first();

            $pos = 0;
            if( !is_null($offline_event->pos_id) ) {
                $pos = FelPOS::where('company_id', $this->company_id)->where('pos_id', $this->pos_id)->first();
            }

            $fel_offline_event_service->setData([
                "codigo_tipo_evento_significativo" => 4,
                "cufd" => $offline_event->cufd,
                "cuis" => $offline_event->cuis,
                "codigoPuntoVenta" => is_null($offline_event->pos_id) ? $pos : $pos->codigo ,
                "codigoSucursal" => $branch->codigo
            ]);

            $response =  $fel_offline_event_service->createSignificantEvent();
            
            $offline_event->fel_significant_event_id = $response['codigo_evento_significativo'];
            $offline_event->save();

            foreach ($package_ids as $key => $value) {
                
                FelInvoiceRequest::where('company_id', $this->company_id)->where('offline_package_id', $key)->chunk( 100, function ( $invoices ) use ($fel_offline_event_service, $value, $key, $branch, $offline_event) {

                    // \Log::debug("Invoices Chuncked: " . json_encode($invoices));
                    // Create Resource for Offline Invoices
                    $resource_class = TypeDocumentResource::getResourceByTypeDocument($value);

                    $invoices = $resource_class::collection($invoices);

                    $file_path = $fel_offline_event_service->createJsonFile($invoices);

                    // Create Zip File
                    $zip_file_path = $fel_offline_event_service->addZipFile( $file_path );
                    
                    unlink($file_path);
                    \Log::debug("File ZIP PATH: " . $zip_file_path);

                    $fel_offline_event_service->setBranchCode( $branch->codigo );
                    $fel_offline_event_service->setTypeDocument( $offline_event->type_document_code );

                    $fel_offline_event_service->setData([
                        [
                            "name" => "codigo_evento_significativo",
                            "contents" => $offline_event->fel_significant_event_id
                        ],
                        [
                        "name" => "archivo",
                        "contents" => Psr7\Utils::tryFopen($zip_file_path, 'r')
                        ]
                    ]);

                    $response = $fel_offline_event_service->sendPackageToFel();

                    $errors = false;
                    if( isset( $response['validation_errors'] ) || isset( $response['facturas'] ) ) {
                        $errors = true;
                    }

                    \DB::table('fel_offline_packages')->where('id', $key)->update([
                        "fel_response" => json_encode( $response ),
                        "has_errors" => $errors,
                        "state" => $errors == true ? FelOfflinePackage::FAILED_STATE : FelOfflinePackage::PROCESSED_STATE,
                        "processed_at" => \Carbon\Carbon::now()->toDateTimeString(),
                    ]);

                    unlink($zip_file_path);

                });

            }

            $offline_event->state = FelOfflineEvent::PROCESSED_STATE;
            $offline_event->processed_at = \Carbon\Carbon::now()->toDateTimeString();
            $offline_event->save();


        } catch( ClientFelException | Exception $ex ) {

            \Log::debug("Error al procesar las Facturas Fuera de Linea: " . $ex->getMessage() . " File: " . $ex->getFile() . " Line:  " . $ex->getLine());

            \DB::table('fel_offline_events')->where('id', $this->offline_event_id)->update([
                'errors' => $ex->getMessage() . " File: " . $ex->getFile() . " Line:  " . $ex->getLine()
            ]);
            
            return;

        }

    }
}
