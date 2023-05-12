<?php

namespace EmizorIpx\ClientFel\Contracts;

use EmizorIpx\ClientFel\Models\FelInvoiceRequest;

interface FelInvoiceBuilderInterface
{

    public function processInput(): void;

    public function insertInputOriginalModel(): void;

    public function getFelInvoice(): FelInvoiceRequest;
    
}
