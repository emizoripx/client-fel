<?php namespace EmizorIpx\ClientFel\Builders;

use EmizorIpx\ClientFel\Contracts\FelInvoiceBuilderInterface;
use EmizorIpx\ClientFel\Models\FelInvoiceRequest;

class FelInvoiceBuilder {


    public function make(FelInvoiceBuilderInterface $builder): FelInvoiceRequest
    {
        $builder->prepare();

        $builder->processInput();

        $builder->createOrUpdate();

        return $builder->getFelInvoice();
    }
}