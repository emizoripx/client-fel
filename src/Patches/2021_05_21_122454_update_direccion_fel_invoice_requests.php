<?php

use EmizorIpx\ClientFel\Models\FelClientToken;
use EmizorIpx\ClientFel\Models\FelInvoiceRequest;
use EmizorIpx\ClientFel\Services\Invoices\Invoices;

class UpdateDireccionFelInvoiceRequests
{
    /**
     * Run the patch.
     *
     * @return void
     */
    public function run()
    {
        
        $felTokens = FelClientToken::all();

        foreach ($felTokens as $feltToken) {
            $this->getAddressInvoice($feltToken);
        }

    }

    public function getAddressInvoice($token){

        $invoice_service = new Invoices($token->host);
        $invoice_service->setAccessToken($token->access_token);

        \Log::info("Update Invoices of Company #". $token->account_id);

        foreach ( FelInvoiceRequest::where('company_id', $token->account_id)->cursor() as $felInvoice) {
            try {
                sleep(2);
                $invoice_service->setCuf($felInvoice->cuf);

                $invoice = $invoice_service->getInvoiceByCuf();

                $felInvoice->update([
                    'direccion' => $invoice['direccion']
                ]);

                \Log::info("Factura #". $felInvoice->numeroFactura . ' Actualizada');
            } catch (Exception $ex) {
                \Log::debug("No se pudo actualizar dirección de la Factura #".$felInvoice->numeroFactura. ' Error: '.$ex->getMessage());
            }
        }

    }
}
