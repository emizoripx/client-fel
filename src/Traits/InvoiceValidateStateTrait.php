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
                // $hashid = new Hashids(config('ninja.hash_salt'), 10);
                // $id_origin_decode = $hashid->decode($fel_invoice->id_origin)[0];

                $invoice = Invoice::withTrashed()->where('id', $fel_invoice->id_origin)->first();

                $invoice->service()->handleCancellation()->deletePdf()->save();

                break;

            case InvoiceStates::ANULACION_RECHAZADA:
                // $hashid = new Hashids(config('ninja.hash_salt'), 10);
                // $id_origin_decode = $hashid->decode($fel_invoice->id_origin)[0];

                $invoice = Invoice::withTrashed()->where('id', $fel_invoice->id_origin)->first();
                $invoice->service()->handleReversal()->save();
                \Log::debug('Anulacion Rechazada ======================');

                break;


            case InvoiceStates::REVERSION_ANULACION_RECHAZADA:
                // $hashid = new Hashids(config('ninja.hash_salt'), 10);
                // $id_origin_decode = $hashid->decode($fel_invoice->id_origin)[0];

                $invoice = Invoice::withTrashed()->where('id', $fel_invoice->id_origin)->first();

                $invoice->service()->handleCancellation()->deletePdf()->save();

                break;
            case InvoiceStates::REVERSION_ANULACION_CONFIRMADA:

                // $hashid = new Hashids(config('ninja.hash_salt'), 10);
                // $id_origin_decode = $hashid->decode($fel_invoice->id_origin)[0];

                $invoice = Invoice::withTrashed()->where('id', $fel_invoice->id_origin)->first();
                $invoice->service()->handleReversal()->save();
                break;
            default:

                break;
        }
    }
}
