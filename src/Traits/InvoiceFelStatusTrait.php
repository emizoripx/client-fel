<?php

namespace EmizorIpx\ClientFel\Traits;

use App\Utils\Ninja;
use EmizorIpx\ClientFel\Events\Invoice\InvoiceWasReversionRevoked;
use EmizorIpx\ClientFel\Events\Invoice\InvoiceWasRevoked;
use EmizorIpx\ClientFel\Exceptions\ClientFelException;
use Exception;

trait InvoiceFelStatusTrait
{

    public function verifyStatusInvoice()
    {
        $felInvoiceRequest = $this->fel_invoice;

        try {

            if (is_null($felInvoiceRequest) || is_null($felInvoiceRequest->cuf)) {
                return -1;
            }

            $felInvoiceRequest->setAccessToken()->sendVerifyStatus();
         
        } catch (ClientFelException $ex) {

            return -1;
        }
    }


}
