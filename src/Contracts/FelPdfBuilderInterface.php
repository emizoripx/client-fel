<?php
namespace EmizorIpx\ClientFel\Contracts;

interface FelPdfBuilderInterface
{
    public function collectData();

    public function useTemplate();

    public function renderHtml(): string;


}
