<?php

namespace EmizorIpx\ClientFel\Traits;

use App\Exceptions\PaymentFailed;
use App\Jobs\Util\SystemLogger;
use App\Models\CompanyGateway;
use App\Models\PaymentHash;
use App\Models\SystemLog;
use Illuminate\Support\Str;

trait PaymentQrTrait {

    public function generateQR()
    {
        \Log::debug("Company ID");
        \Log::debug($this->company_id);
        $gateway = CompanyGateway::whereCompanyId($this->company_id)->whereGatewayKey('d14dd26a47cec830x11x5700bfb67b34')->first();
        if (empty($gateway) ) return ;
        $payment_method_id = 1000;
        $tokens = [];

        $invoice_total = $this->amount;
        $hash_data =['invoices' => [(object)["invoice_id"=>$this->id,"amount"=> $invoice_total,"due_date"=>"","invoice_number"=>$this->number,"additional_info"=>""]], 'credits' => 0, 'amount_with_fee' => $this->amount ];

        $payment_hash = new PaymentHash;
        $payment_hash->hash = Str::random(32);
        $payment_hash->data = $hash_data;
        $payment_hash->fee_total = 0;
        $payment_hash->fee_invoice_id = $this->id;

        $payment_hash->save();

        $payment_invoices = collect([(object)["invoice_id" => $this->hashed_id, "amount" => $invoice_total, "due_date" => "", "invoice_number" => $this->number, "additional_info" => ""]]);

        $totals = [
            'credit_totals' => 0,
            'invoice_totals' => $invoice_total,
            'fee_total' => 0,
            'amount_with_fee' => $invoice_total,
        ];

        if ($gateway) {
            $tokens =$this->client->gateway_tokens()
                ->whereCompanyGatewayId($gateway->id)
                ->whereGatewayTypeId($payment_method_id)
                ->get();
        }

        $data = [
            'payment_hash' => $payment_hash->hash,
            'total' => $totals,
            'invoices' => $payment_invoices,
            'tokens' => $tokens,
            'payment_method_id' => $payment_method_id,
            'amount_with_fee' => $invoice_total,
        ];

        try {
                return $gateway
                    ->driver($this->client)
                    ->setPaymentMethod($payment_method_id)
                    ->setPaymentHash($payment_hash)
                    ->generateQR($data);
        } catch (\Exception $e) {
            SystemLogger::dispatch(
                $e->getMessage(),
                SystemLog::CATEGORY_GATEWAY_RESPONSE,
                SystemLog::EVENT_GATEWAY_ERROR,
                SystemLog::TYPE_FAILURE,
                $this->client,
                $this->client->company
            );

            \Log::debug("Error Payment ". $e->getMessage(). "File ". $e->getFile(). "Line ". $e->getLine());
            throw new PaymentFailed($e->getMessage());
        }
    }   

}