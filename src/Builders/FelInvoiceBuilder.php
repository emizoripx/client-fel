<?php namespace EmizorIpx\ClientFel\Builders;

use EmizorIpx\ClientFel\Contracts\FelInvoiceBuilderInterface;
use EmizorIpx\ClientFel\Jobs\AddOfflinePackage;
use EmizorIpx\ClientFel\Models\FelInvoiceRequest;
use Exception;

class FelInvoiceBuilder {


    public function make(FelInvoiceBuilderInterface $builder): FelInvoiceRequest
    {
        $builder->prepare();

        $builder->processInput();
        
        try{
            $builder->createOrUpdate();
        }catch(Exception $ex) {
            \Log::debug($ex->getMessage());
        }
        
        $builder->changeOriginalTotal($builder->getFelInvoice());

        if( isset( $builder->getFelInvoice()->cuf ) ) {

            AddOfflinePackage::dispatch( $builder->getFelInvoice() );
        }

        return $builder->getFelInvoice();
    }
}