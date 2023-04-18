<?php

namespace EmizorIpx\ClientFel\Jobs;

use EmizorIpx\ClientFel\Exceptions\ClientFelException;
use EmizorIpx\ClientFel\Models\FelInvoiceRequest;
use EmizorIpx\ClientFel\Utils\InvoiceStates;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Throwable;

class GetInvoiceStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    protected $action;

    protected $fel_invoice;

    protected $delay_times = [ 10, 20, 30, 60, 120, 300 ];

    protected $delay_offline = [ 1800, 3600, 10800, 18000, 36000, 86400 ];

    public $tries = 6;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( FelInvoiceRequest $fel_invoice, $action )
    {
        $this->fel_invoice = $fel_invoice;
        $this->action = $action;

    }


    public function middleware() {

        return [(new WithoutOverlapping($this->fel_invoice->id))->releaseAfter(10)];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {

            $this->fel_invoice->refresh();

            if( empty( $this->fel_invoice->cuf ) || is_null( $this->fel_invoice->cuf ) ) {

                \Log::debug('GET INVOICE STATUS JOBS ----------- ' . $this->fel_invoice->id . ' La Factura no tiene CUF');
                $this->fail();
            }

            $response = $this->fel_invoice->setAccessToken()->sendVerifyStatus();

            \Log::debug('GET INVOICE STATUS JOBS ------------- ' . $this->fel_invoice->id . ' status response: ' . json_encode($response) );


            if( is_null( $response['codigoEstado'] ) || ! in_array( $response['codigoEstado'], InvoiceStates::getFinalStatusArray($this->action) ) ){

                throw new ClientFelException( 'Factura Pendiente' );
            }


            $this->fel_invoice->update([
                'codigoEstado' => $response['codigoEstado'],
                'estado' => $response['estado'],
                'errores' => $response['errores']
            ]);

            if( $this->action == InvoiceStates::REVOCATE_ACTION && $response['codigoEstado'] == 691 ){

                \Log::debug('Cancellation Invoice: ' . $this->fel_invoice->id_origin);

                $invoice = $this->fel_invoice->invoice_origin();
                $invoice = $invoice->service()->handleCancellation()->deletePdf()->touchPdf()->save();
            }

        } catch( ClientFelException | Exception $ex ) {
            \Log::debug('GET INVOICE STATUS JOBS ---------- Log Exception ' . $ex->getMessage() . ' File: ' . $ex->getFile() . ' Line: ' . $ex->getLine() );

            $attempts_after = $this->delay_times[ $this->attempts() - 1 ];

            if( $this->fel_invoice->emission_type == 'Fuera de línea' ){

                $attempts_after = $this->delay_offline[$this->attempts() - 1];
            }

            \Log::debug('GET INVOICE STATUS JOBS ----------- Invoice #' . $this->fel_invoice->numeroFactura . ' Attempt after of ' . $attempts_after . ' seconds');

            $this->release( $attempts_after );
            
        }
    }

    public function failed( Throwable $exception ) {

        \Log::debug('GET INOVICE STATUS JOBS ----------- ocurrio un error en realizar la petición de Estado Exception: ' . $exception->getMessage() );

        //Notificar por correo
    }
}
