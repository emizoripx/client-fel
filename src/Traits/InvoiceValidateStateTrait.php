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

                $invoice->service()->handleCancellation()->save();

                break;

            case InvoiceStates::ANULACION_RECHAZADA:

                $invoice = Invoice::withTrashed()->where('id', $fel_invoice->id_origin)->first();
                $invoice->service()->reverseCancellation()->save();
                break;
            default:

                break;
        }
    }
}
