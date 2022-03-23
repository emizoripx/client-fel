<?php

namespace EmizorIpx\ClientFel\PdfBuilders;

use EmizorIpx\ClientFel\Models\FelInvoiceRequest;

class BasePdfBuiler{

    protected $fel_invoice_request;
    public function __construct(FelInvoiceRequest $fel_invoice_request)
    {
        $this->fel_invoice_request = $fel_invoice_request;
    }

}