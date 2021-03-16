<?php
namespace EmizorIpx\ClientFel\Traits;


trait InvoiceFelHtml {

    public function getHtmlUrl($host)
    {
        \Log::debug("url:  " . $host);
        $url = $host . "/factura-html/" . $this->cuf;
        \Log::debug("url:  " . $url);

        return $url;
    }
}