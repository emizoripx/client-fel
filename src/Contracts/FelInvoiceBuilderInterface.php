<?php

namespace EmizorIpx\ClientFel\Contracts;

use EmizorIpx\ClientFel\Models\FelInvoiceRequest;

interface FelInvoiceBuilderInterface
{

    public function prepare(): FelInvoiceRequest;

    public function processInput(): FelInvoiceRequest;

    public function createOrUpdate(): void;

    public function getFelInvoice(): FelInvoiceRequest;
    
}
