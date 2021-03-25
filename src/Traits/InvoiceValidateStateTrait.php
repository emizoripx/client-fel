<?php

namespace EmizorIpx\ClientFel\Traits;

use App\Models\Invoice;
use App\Repositories\InvoiceRepository;
use EmizorIpx\ClientFel\Exceptions\ClientFelException;
use EmizorIpx\ClientFel\Utils\InvoiceStates;
use Hashids\Hashids;

trait InvoiceValidateStateTrait{

    public function validateStateCode($value, $fel_invoice){
        
        switch ($value) {
            case InvoiceStates::ANULACION_CONFIRMADA:
                $fel_invoice->delete();
                break;
            
            case InvoiceStates::ANULACION_RECHAZADA:
                $invoice_repo = new InvoiceRepository();

                $hashid = new Hashids(config('ninja.hash_salt'), 10);
                $id_origin_decode = $hashid->decode($fel_invoice->id_origin)[0];
                
                $invoice = Invoice::withTrashed()->where('id', $id_origin_decode)->first();
                \Log::debug('Anulacion Rechazada ======================');
                \Log::debug($invoice);
                
                if(!is_null($invoice->deleted_at)){

                    $invoice_repo->restore($invoice);
                }

                break;

                
                case InvoiceStates::REVERSION_ANULACION_RECHAZADA:
                    $invoice_repo = new InvoiceRepository();
    
                    $hashid = new Hashids(config('ninja.hash_salt'), 10);
                    $id_origin_decode = $hashid->decode($fel_invoice->id_origin)[0];
                    
                    $invoice = Invoice::withTrashed()->where('id', $id_origin_decode)->first();
    
                    \Log::debug('Reversion Rechazada ======================');
                    \Log::debug($invoice);
                    
                    if(is_null($invoice->deleted_at)){
    
                        $invoice_repo->delete($invoice);
                    }
    
                    break;
                case InvoiceStates::REVERSION_ANULACION_CONFIRMADA:
                    
                    $fel_invoice->restoreInvoice();
                    break;
            default:
                
                break;
        }
    }
}