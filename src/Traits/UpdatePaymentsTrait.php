<?php

namespace EmizorIpx\ClientFel\Traits;

use App\Factory\PaymentFactory;
use App\Models\Invoice;
use App\Repositories\CreditRepository;
use App\Repositories\PaymentRepository;

trait UpdatePaymentsTrait {

    public function updateInvoicePayments( $new_invoice, $invoice_ids ) {

        \Log::debug("Update Payments for invoices IDs: " . $invoice_ids);

        $invoice_ids_exploded = explode(',', $invoice_ids);

        if ( count($invoice_ids_exploded) <= 1 ) {
            \Log::debug("Uncombined Invoices");
            return;
        }

        $invoice_ids_exploded = array_map( function ($item) {
            return intval($this->decodePrimaryKey($item));
        }, $invoice_ids_exploded );

        \Log::debug("Hash Decoded: " , [$invoice_ids_exploded]);

        $invoices = Invoice::whereIn('id', $invoice_ids_exploded)->get();

        if( !$invoices ) {
            \Log::debug("Invoices not found");

            return;
        }

        $balance = 0;
        $paid_to_date = 0;
        $payments_array = [];

        foreach ($invoices as $invoice) {

            $balance += $invoice->balance;
            $paid_to_date += $invoice->paid_to_date;

            $payments_data = isset($invoice->payments) ? json_decode(json_encode($invoice->payments), true) : null;

            \Log::debug("Payment Array: " . gettype($payments_array));
            \Log::debug("Payment Array: " . gettype($payments_data));
            // \Log::debug("Payment Data: " . json_encode($payments_data));

            if( $payments_data ) {
                \Log::debug("Payment Merge");
                $payments_array = array_merge($payments_array, $payments_data);
            }
            

        }

        $payment_repo = new PaymentRepository( new CreditRepository);

        \Log::debug("New Payment Created ID: " . json_encode($payments_array));

        foreach ($payments_array as $payment) {
            
            $data = [
                'amount' => floatval($payment['amount']),
                'client_id' => $new_invoice->client_id,
                'date' => $payment['date'],
                'invoices' => [
                    [
                        'invoice_id' => $new_invoice->id,
                        'amount' => floatval($payment['pivot']['amount']),
                    ]
                ],
            ];

            $payment = $payment_repo->save($data, PaymentFactory::create( auth()->user()->company()->id, auth()->user()->id ));

            \Log::debug("New Payment Created ID: " . $payment->id);

        }

        \Log::debug("Balance: " . $balance);
        \Log::debug("Paid To Date: " . $paid_to_date);
        \Log::debug("Payments: " . json_encode($payments_array));

    }
    
}