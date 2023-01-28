<?php

namespace EmizorIpx\ClientFel\Jobs;

use App\Models\Invoice;
use App\Models\Payment;
use EmizorIpx\ClientFel\Models\FelFileNotification;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class BiocenterStatusNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $invoice;

    protected $payment;

    protected $fel_invoice;

    protected $base_path = "emizor/dir_txt.factura";

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( Invoice $invoice, Payment $payment = null)
    {
        $this->invoice = $invoice;

        $this->payment = $payment;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        \Log::debug("JOB BIOCENTER NOTIFICATION >>>>>>>>>>>>>>>>>> INIT: INVOICE ID " . $this->invoice->id);

        try {

            if($this->invoice->company->settings->id_number != '168114022') {

                \Log::debug("Not File Notification >>>>>>>>>> ");

                return;
            }
            // $this->invoice = Invoice::whereId(4686)->first();

            // $this->payment = Payment::whereId(957)->first();

            // \Log::debug("Invoice: " . json_encode($this->invoice));

            $file_notification = FelFileNotification::create([
                'company_id' => $this->invoice->company_id,
                'invoice_id' => $this->invoice->id,
                'payment_id' => is_null($this->payment) ? NULL : $this->payment->id,
                'type' => FelFileNotification::INVOICE_TYPE_NOTIFICATION
            ]);
            
            $this->fel_invoice = $this->invoice->fel_invoice;

            // \Log::debug("Invoice: " . json_encode($this->fel_invoice));

            $file_name = $this->fel_invoice->codigoSucursal . "-". $this->fel_invoice->numeroFactura;

            \Log::debug($file_name);

            $state = $this->getInvoiceStatus();

            $payment_method = is_null($this->payment) ? 0 : strtoupper(__('texts.payment_type_'.$this->payment->type->name));

            $order_codes = $this->fel_invoice->getVariableExtra('orders');

            $content = "$file_name \n$state \n$payment_method \n$order_codes";

            
            $res1 = Storage::disk('biocenter-s3')->put($this->base_path .'/'.$file_name . ".txt", $content);
            $res2 = Storage::disk('s3')->put('biocenter-txt/' . $this->base_path .'/'.$file_name . ".txt", $content);

            $path_s3 = Storage::disk('s3')->url('biocenter-txt/' . $this->base_path .'/'.$file_name . ".txt");

            $file_notification->file_path = $path_s3;
            $file_notification->content = $content;
            $file_notification->status = FelFileNotification::PROCESSED_STATUS;
            $file_notification->save();

            \Log::debug("Process: " . $path_s3);
            \Log::debug("Process");

        } catch( Exception $ex) {

            \Log::debug("Error Al Notificar Archivo: " . $ex->getMessage() . " File: " . $ex->getFile() . " Line: " . $ex->getLine());

            if( isset($file_notification) ) {
                $file_notification->status = FelFileNotification::FAILED_STATUS;
                $file_notification->errors = $ex->getMessage();
                $file_notification->save();
            }

        }

        \Log::debug("JOB BIOCENTER NOTIFICATION >>>>>>>>>>>>>>>>>> END");
    }

    public function getInvoiceStatus(  ){

        $status = '';

        if( $this->invoice->paid_to_date == 0 ) {

            $status = 'POR COBRAR';
        } elseif($this->invoice->paid_to_date < $this->invoice->amount) {

            $status = 'PAGO PARCIAL';

        } elseif( $this->invoice->paid_to_date == $this->invoice->amount ) {

            $status = 'PAGADO';

        }

        if( $this->fel_invoice->codigoEstado == 691 ) {
            $status = 'ANULADO';
        }

        return $status;

    }
}
