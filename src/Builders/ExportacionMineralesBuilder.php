<?php

namespace EmizorIpx\ClientFel\Builders;

use EmizorIpx\ClientFel\Contracts\FelInvoiceBuilderInterface;
use EmizorIpx\ClientFel\Models\FelInvoiceRequest;

class ExportacionMineralesBuilder extends BaseFelInvoiceBuilder implements FelInvoiceBuilderInterface
{

    protected $fel_invoice;

    public function __construct($data)
    {
        parent::__construct($data);
    }

    public function prepare(): FelInvoiceRequest
    {
        if ($this->source_data['update'])
            $this->fel_invoice = FelInvoiceRequest::whereIdOrigin($this->source_data['model']->id)->whereNull('cuf')->firstOrFail();
        else
            $this->fel_invoice = new FelInvoiceRequest();

        return $this->fel_invoice;
    }

    public function processInput(): FelInvoiceRequest
    {
        $input = array_merge(
            $this->input,
            $this->getDetailsAndTotals()
        );

        $this->fel_invoice->fill($input);

        return $this->fel_invoice;
    }

    public function getFelInvoice(): FelInvoiceRequest
    {
        return $this->fel_invoice;
    }

    public function createOrUpdate(): void
    {
        $this->fel_invoice->save();
    }
    
}
