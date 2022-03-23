<?php

use EmizorIpx\ClientFel\Contracts\FelPdfBuilderInterface;
use EmizorIpx\ClientFel\PdfBuilders\BasePdfBuiler;
use EmizorIpx\ClientFel\Utils\TypeDocumentSector;

class CompraVentaPdfBuilder extends BasePdfBuiler implements FelPdfBuilderInterface
{
    protected $template_blade;

    protected $data;

    public function collectData()
    {

        $emizor_logo = 'data:image/jpg;base64,' . base64_encode(file_get_contents('https://s3.amazonaws.com/EMIZOR/emizorv5_logo.jpg'));

                $logo = 'data:image/jpg;base64,' . base64_encode(file_get_contents($company->present()->logo()));

        $branch =  $this->fel_invoice_request->getBranchByCode();

        $felinvoice = $this->fel_invoice_request;

        $fel_company = $this->fel_invoice_request->felCompany();
        $unipersonal =  boolval($fel_company->is_uniper);
        $business_name =  $fel_company->business_name;
        $EMIZOR_ENVIRONMENT_CODE = $fel_company->production;
        
        $company_id = $this->fel_invoice_request->company_id;
        
        $leyenda = $this->fel_invoice_request->getLeyenda();




        $nitemisor = $company->settings->id_number;
        $company_name =  $company->settings->name;


        


        $qr = \QrCode::generate($felinvoice->getUrlSin());

        $montoliteral = 'SON: ' . $this->getToWord(($felinvoice->type_document_sector_id == 16 ? $felinvoice->montoTotal - $felinvoice->montoGiftCard : $felinvoice->montoTotal), 2, Currencies::getDescriptionCurrency($felinvoice->codigoMoneda));

        $terms = $this->terms;
        
        $this->data = compact('felinvoice', 'branch', 'logo', 'nitemisor', 'leyenda', 'montoliteral', 'qr', 'emizor_logo', 'EMIZOR_ENVIRONMENT_CODE',  'company_name', 'business_name', 'unipersonal', 'terms');
    }
    
    public function useTemplate()
    {
        $this->template_blade = TypeDocumentSector::geTemplateByCode($this->fel_invoice_request->type_document_sector_id, $nitemisor);
    }

    public function renderHtml(): string
    {
        return view('pdf-designs.' . $this->template_blade, $this->data)->render();
    }
}