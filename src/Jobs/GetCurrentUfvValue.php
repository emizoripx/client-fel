<?php

namespace EmizorIpx\ClientFel\Jobs;

use EmizorIpx\ClientFel\Exceptions\ClientFelException;
use EmizorIpx\ClientFel\Models\FelUfv;
use EmizorIpx\ClientFel\Services\BCB\BCBService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class GetCurrentUfvValue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 5;

    protected $delay_time = 10800;

    protected $bcb_service;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle( BCBService $bcb_service )
    {
        \Log::debug("GET UFV VALUE JOBS >>>>>>>>>>>>>>>>>>>>>> INIT - Attempts #". $this->attempts());

        $this->bcb_service = $bcb_service;

        $current_day_month = Carbon::now()->format('Y-m-d');

        \Log::debug("Current Date: " . $current_day_month);



        try {
            $ufv_value = FelUfv::where('fecha', $current_day_month)->first();

            if( ! $ufv_value ) {
                \Log::debug("Obtener Valor Para Fecha:: " . $current_day_month);

                $this->getAndCreateUfvValue($current_day_month);
            }


            $last_day_month = Carbon::parse($current_day_month)->endOfMonth()->format('Y-m-d');
            
            \Log::debug("Get Value of Last Day of Month: " . $last_day_month );

            $ufv_value = FelUfv::where('fecha', $last_day_month)->first();

            if( ! $ufv_value ) {
                \Log::debug(" Obtener Valor Para Fecha: " . $last_day_month);

                $this->getAndCreateUfvValue($last_day_month);
            }



            \Log::debug("GET UFV VALUE JOBS >>>>>>>>>>>>>>>>>>>>>> FIN");

        } catch( ClientFelException $ex ) {

            \Log::debug("Ocurrio un error al obtener el UFV: " . $ex->getMessage());

            $this->release($this->delay_time);

        }

    }

    public function getAndCreateUfvValue ( $day_month ) {
        
        $this->bcb_service->setStartDate($day_month);
        $this->bcb_service->setEndDate($day_month);

        $response = $this->bcb_service->getUfvValue();

        if( empty( $response ) ) {

            \Log::debug("No se obtuvo una respuesta del servicio");

            throw new ClientFelException('No se obtuvo una respuesta del servicio ');

        }

        $ufv_value = number_format($response[0]['val_ufv'], 5);

        \Log::debug("Valor UFV Obtenido: " . $ufv_value);

        FelUfv::create([
            'fecha' => $day_month,
            'val_ufv' => $ufv_value,
        ]);

    }

    public function failed(Throwable $exception)
    {
        // Send user notification of failure, etc...
        \Log::debug("Ocurrio un Error al Obtener el valor UFV: " . $exception->getMessage());

        // Notification::route('mail', 'remberto.molina@emizor.com')->notify( new GetStatusInvoiceFailed($this->invoice, $exception->getFile() , $exception->getLine(), $exception->getMessage()) );

    }
}
