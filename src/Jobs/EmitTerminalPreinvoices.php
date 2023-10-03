<?php

namespace EmizorIpx\ClientFel\Jobs;

use App\Models\Company;
use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;
use EmizorIpx\ClientFel\Exceptions\ClientFelException;
use EmizorIpx\ClientFel\Utils\TypeDocumentSector;
use Exception;

class EmitTerminalPreinvoices implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 1000; // EMIZOR-INVOICE-INSERT

    public $tries = 1; // EMIZOR-INVOICE-INSERT
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->onQueue('recurring');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            \Log::debug("EMIT TERMIAL INVOICES JOB >>>>>>>>>>>>>>>>> INIT");
            $company = Company::where('settings->id_number', '347399028')->first();

            if( empty($company) ){
                \Log::debug("Compañia no encontrada");
                return;
            }

            $now_date = Carbon::now();

            \Log::debug("Mes Actual: " . $now_date->month);
            \Log::debug("INICIO DE PROCESO: " . $now_date);
            // $last_day = $now_date->lastOfMonth();

            \Log::debug("Company " . $company->id);

            $invoices = Invoice::join('fel_invoice_requests', 'invoices.id', '=', 'fel_invoice_requests.id_origin')
                        ->where('fel_invoice_requests.company_id', $company->id)
                        ->where('fel_invoice_requests.type_document_sector_id', TypeDocumentSector::ALQUILER_BIENES_INMUEBLES)
                        ->whereNull('fel_invoice_requests.cuf')
                        ->whereMonth('invoices.created_at', $now_date->month)
                        ->select('invoices.*')
                        ->get();


            \Log::debug("Preinvoices: " . json_encode($invoices));

            foreach ($invoices as $invoice) {
                
                try {

                    \Log::debug("Emit Invoice Number: " . $invoice->numeroFactura);

                    $invoice = $invoice->service()->applyNumber()->save();

                    $invoice->service()->emit('true');

                } catch ( ClientFelException $cex ) {

                    \Log::debug("Ocurrio a emitir la Factura: " . $invoice->numeroFactura . " Error: " . $cex->getMessage());

                }

            }

            \Log::debug("Se emitió " . count($invoices) . " Facturas");
            \Log::debug("EMIT TERMIAL INVOICES JOB >>>>>>>>>>>>>>>>> END");

        } catch( \Exception $ex ) {

            \Log::debug("Error al Obtener las prefacturas: " . $ex->getMessage());

            return;

        }



    }
}
