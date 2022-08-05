<?php

namespace EmizorIpx\ClientFel\Jobs;

use EmizorIpx\ClientFel\Exceptions\ClientFelException;
use EmizorIpx\ClientFel\Models\FelClientToken;
use EmizorIpx\ClientFel\Models\FelOfflineEvent;
use EmizorIpx\ClientFel\Models\FelOfflinePackage;
use EmizorIpx\ClientFel\Services\OfflineEvent\FelOfflineEventService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GetSignificantEventStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $offline_event;

    protected $delay_times = [60, 120, 180, 240 , 300];

    protected $tries = 5;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( FelOfflineEvent $offline_event )
    {
        
        $this->offline_event = $offline_event;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            \Log::debug("GET SIGNIFICANT EVENT STATUS JOBS >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> INIT");

            $company_token = FelClientToken::where('account_id', $this->offline_event->company_id)->firstOrFail();
    
            if( ! $company_token ) {
                throw new Exception("No se encontro credenciales para la compañía:  " . $this->company_id);
            }
    
            $fel_offline_event_service = new FelOfflineEventService( $company_token->host );
    
            $fel_offline_event_service->setAccessToken( $company_token->access_token );

            $fel_offline_event_service->setSignificantEventCode( $this->offline_event->fel_significant_event_id );

            $response = $fel_offline_event_service->getSignificantEventStatus();

            \Log::debug("Reponse GetStatus Event: " . json_encode( $response ));

            if( isset($response['status']) && $response['status'] != 'success' ){

                \Log::debug("Errors: " . json_encode($response['errors']));
                
                $this->dontRelease();

            }

            if( $response['data']['estado'] != 'Cerrado' ) {

                $attemp_after = $this->delay_times[ $this->attempts() - 1 ];

                $this->release( $attemp_after );
            }

            \Log::debug("Offline Event to get: " . json_encode($this->offline_event));

            $package_ids = FelOfflinePackage::where('company_id', $this->offline_event->company_id)->where('offline_event_id', $this->offline_event->id)->pluck('id');

            \DB::table('fel_invoice_requests')->where('company_id', $this->offline_event->company_id)->whereIn('offline_package_id', $package_ids )->update([
                'estado' => "VALIDO",
                'codigoEstado' => 690
            ]);

            if( count($response['data']['errores']) > 0 ) {

                foreach ($response['data']['errores'] as $key => $value) {
                    
                    \Log::debug("Error: " . json_encode($value));

                    \DB::table('fel_invoice_requests')->where('cuf', $key)->update([
                        'estado' => 'INVALIDO',
                        'codigoEstado' => 904,
                        'urlSin' => NULL,
                        'errores' => json_encode($value),
                    ]);

                }

            }

            \Log::debug("GET SIGNIFICANT EVENT STATUS JOBS >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> END");
            
        } catch ( ClientFelException | Exception $ex ) {


            \Log::debug("Error al obtener estado del evento: " . $ex->getMessage());

            $attemp_after = $this->delay_times[ $this->attempts() - 1 ];

            $this->release( $attemp_after );

        }
    }

    public function failed(Throwable $exception)
    {
        // Send user notification of failure, etc...
        \Log::debug("Ocurrio un Error en realizar la Peticion de Estado Excepción: " . $exception->getMessage());

    }
}
