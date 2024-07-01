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
            $now_date = now()->settimeZone(config('app.timezone'));
            $first_day = "2024-06-01 00:00:00";
            $last_day  = "2024-06-30 23:59:59";
            $company = Company::where('settings->id_number', '347399028')->select("id")->first();


            if (empty($company)) {
               logger()->error("EMIT-TERMINAL NOT FOUND");
                return;
            }

            $tms = "EMIT-TERMINAL #" . $company->id;


            info($tms ."Periodo " . $first_day . " - " . $last_day);
            // $this->info($tms ."Periodo " . $first_day . " - " . $last_day);

            $invoices = Invoice::join('fel_invoice_requests', function ($join) use($company) {
                $join->on('invoices.id', '=', 'fel_invoice_requests.id_origin')
                ->where('fel_invoice_requests.company_id', $company->id)
                ->where('fel_invoice_requests.type_document_sector_id', 2)
                ->whereNull('fel_invoice_requests.cuf')
                ->whereNull('fel_invoice_requests.recurring_id_origin')
                ->whereNull('fel_invoice_requests.deleted_at');
            })
            ->where('invoices.company_id', $company->id)
            ->whereBetween('invoices.created_at', [$first_day, $last_day])
            ->select('invoices.*');

            $invoices->cursor()->each(function($invoice) use ($tms){
                try {
                    info($tms .  "Emit Invoice Number: " . $invoice->number);
                    // $this->info($tms .  "Emit Invoice Number: " . $invoice->number);

                    $invoice = $invoice->service()->applyNumber()->save();
                    info($tms . "invoice_id=" . $invoice->id);
                    
                    // $this->info($tms . "invoice_id=" . $invoice->id);

                    $invoice->service()->emit('true');

                    info($tms . "invoice_id=" . $invoice->id . " EMITTED");
                    // $this->info($tms . "invoice_id=" . $invoice->id . " EMITTED");

                } catch (\Throwable $th) {
                    logger()->error($tms . " File:".$th->getFile() . " Line:".$th->getLine()." message: ".$th->getMessage());
                    // $this->info($tms . " File:".$th->getFile() . " Line:".$th->getLine()." message: ".$th->getMessage());
                }

            });

            info($tms . "FINISH");
            // $this->info($tms . "FINISH");


        } catch(\Throwable $th ) {

            logger()->error("EMIT-TERMINAL File: ".$th->getFile() . " Line:" . $th->getLine() . " message: " . $th->getMessage() );
            return;

        }
    }
}
