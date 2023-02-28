<?php

namespace EmizorIpx\ClientFel\Jobs;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Exception;

class EmitInvoiceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $invoice_id;
    public function __construct($id)
    {
        $this->invoice_id = $id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            \Log::debug("\n\n\n\n\n\n\n============Emitir factura en background==============\n\n\n\n\n\n");
          

            $invoice = Invoice::find($this->invoice_id);
            if (!is_null($invoice->fel_invoice) ) {
                if ($invoice->fel_invoice->cuf) {
                    \Log::debug("Actualizar factura......");
                    $invoice->service()->updateEmitedInvoice('true');
                } else {
                    \Log::debug("Emitir prefactura......");
                    $invoice->service()->emit('true');
                }
            }

            \Log::debug("EMIT TERMIAL INVOICES JOB >>>>>>>>>>>>>>>>> END");
        } catch (\Exception $ex) {

            \Log::debug("Error al emitir la factura: " . $ex->getMessage());

            return;
        }
    }
}
