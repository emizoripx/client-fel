<?php

namespace EmizorIpx\ClientFel\Services\Invoices;

use App\Models\Invoice;
use App\Services\AbstractService;

class HandleCancellationPending extends AbstractService {

    private $invoice;

    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    public function run()
    {
        /* Check again!! */
        if (! $this->invoice->invoiceCancellable($this->invoice)) {
            return $this->invoice;
        }

        $this->invoice = $this->invoice->service()->setStatus(Invoice::STATUS_CANCELLATION_PENDING)->save();

        return $this->invoice;
    }
}
