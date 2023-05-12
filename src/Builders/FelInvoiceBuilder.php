<?php namespace EmizorIpx\ClientFel\Builders;

use EmizorIpx\ClientFel\Contracts\FelInvoiceBuilderInterface;
use EmizorIpx\ClientFel\Models\FelInvoiceRequest;
use Exception;

class FelInvoiceBuilder {


    public function make(FelInvoiceBuilderInterface $builder): FelInvoiceRequest
    {
        $builder->prepare();

        $builder->processInput();
        
        $builder->insertInputOriginalModel();

        $builder->changeOriginalTotal($builder->getFelInvoice());

        return $builder->getFelInvoice();
    }
}