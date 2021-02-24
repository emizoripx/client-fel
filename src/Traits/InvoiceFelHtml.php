<?php
namespace EmizorIpx\ClientFel\Traits;


trait InvoiceFelHtml {

    public function getHtmlUrl()
    {
        $url = config('clientfel.api_url') . "/factura-html/" . $this->cuf;
        \Log::debug("url:  " . $url);

        return $url;
    }
}