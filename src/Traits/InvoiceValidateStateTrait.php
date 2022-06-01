<?php

namespace EmizorIpx\ClientFel\Traits;

use App\Models\Invoice;
use App\Repositories\InvoiceRepository;
use EmizorIpx\ClientFel\Exceptions\ClientFelException;
use EmizorIpx\ClientFel\Utils\InvoiceStates;
use Hashids\Hashids;

trait InvoiceValidateStateTrait
{

    public function validateStateCode($value, $fel_invoice)
    {

        switch ($value) {
            case InvoiceStates::ANULACION_CONFIRMADA:
                $invoice = Invoice::withTrashed()->where('id', $fel_invoice->id_origin)->first();

                $invoice->service()->handleCancellation()->deletePdf()->save();

                break;

            case InvoiceStates::ANULACION_RECHAZADA:

                $invoice = Invoice::withTrashed()->where('id', $fel_invoice->id_origin)->first();
                $invoice->service()->reverseCancellation()->save();
                break;

            case InvoiceStates::INVOICE_VALID_STATUS_CODE :

                $invoice = Invoice::withTrashed()->where('id', $fel_invoice->id_origin)->first();
                if( $invoice->status_id == Invoice::STATUS_CANCELLED ){
                    \Log::debug("Reverse Cancellation Invoice #". $fel_invoice->numeroFactura);
                    $invoice->service()->reverseCancellation()->save();
                }
                break;
            default:

                break;
        }
    }
}
