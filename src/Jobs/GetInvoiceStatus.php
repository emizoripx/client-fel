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

    public $tries = 0; 

    public $timeout = 0;
    // 6 seg, 6 seg, 30 seg, 30 seg, 1 min, 1 min, 5 min, 10 min 30 min
    public $backoff = [0.1, 0.1,0.5,0.5,1,1,5,10,30];
    // 6 seg, 30 seg, 5 min, 30 min 60 min
    public $backoffoffline = [0.1, 0.5, 5, 30, 60];
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

    public function retryUntil()
    {
        return now()->addHours(48); // two days
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
        $tms = "GET-INVOICE-STATUS >>> #" . $this->fel_invoice->id . " >> ACTION = ". $this->action. " >>> ";
        try {

            $this->fel_invoice->refresh();

            if( empty( $this->fel_invoice->cuf ) ) {

                info($tms. "CUF NOT FOUND");
                return;
                // $this->fail();
            }
            $response = $this->fel_invoice->setAccessToken()->sendVerifyStatus();
            
            info($tms. "FEL response ". json_encode($response));

            if( is_null( $response['codigoEstado'] ) || ! in_array( $response['codigoEstado'], InvoiceStates::getFinalStatusArray($this->action) ) ){
                info($tms. "Fel invoice request status " , [$this->fel_invoice->emission_type]);


                if ($this->action == InvoiceStates::REVOCATE_ACTION && $response['codigoEstado'] == 690 && !empty($response["errores"])  ) {
                        $used_or_consolidated = array_filter(
                            json_decode($response["errores"], false),
                            function ($e) {
                                return $e->code == 997 || $e->code == 3010;
                            }
                        );

                        if (!empty($used_or_consolidated)) {
                            info($tms. "CONSOLIDADA!!!");
                            info($tms. "REVERTING INVOICE TO VALID !!!");
                            $invoice = $this->fel_invoice->invoice_origin();
                            $invoice = $invoice->service()->setCalculatedStatus()->touchPdf(true)->save();

                            // TODO: send email or notification to user
                            return;
                        }
                }

                if( $this->fel_invoice->emission_type == 'Fuera de línea' ){

                    $this->releaseBackoff("offline");
                }

                $this->releaseBackoff();
            }

            $this->fel_invoice->update([
                'codigoEstado' => $response['codigoEstado'],
                'estado' => $response['estado'],
                'errores' => $response['errores']
            ]);
            
            info($tms. "updated status code");
            if( $this->action == InvoiceStates::REVOCATE_ACTION && $response['codigoEstado'] == 691 ){

                \Log::debug('Cancellation Invoice: ' . $this->fel_invoice->id_origin);

                $invoice = $this->fel_invoice->invoice_origin();
                $invoice = $invoice->service()->handleCancellation()->deletePdf()->touchPdf()->save();
            }

        } catch( \Throwable $ex ) {
            info($tms. "ERRROR " . $ex->getMessage() . ' File: ' . $ex->getFile() . ' Line: ' . $ex->getLine());
            // TODO: NOTIFY BY EMAIL
            return;
        }
    }

    public function releaseBackoff($type="")
    {
        if ($this->attempts() > sizeof($this->{"backoff".$type})) {
            $retry_time= $this->{"backoff".$type}[sizeof($this->{"backoff".$type})-1] * 60 ;
        }else {
            $retry_time= $this->{"backoff".$type}[$this->attempts()-1] * 60 ;
        }

        info("GET-INVOICE-STATUS >>> #" . $this->fel_invoice->id . " >> ACTION = ". $this->action. " >> ATTEMPT = " . $this->attempts() . " >>> RELEASE FOR " . $retry_time . " seconds");
        $this->release($retry_time); 
    }
}
