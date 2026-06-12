<?php

namespace EmizorIpx\ClientFel\Http\Controllers;

use App\Http\Controllers\BaseController;
use EmizorIpx\ClientFel\Models\BitacoraLog;
use EmizorIpx\ClientFel\Models\FelClientToken;
use EmizorIpx\ClientFel\Repository\FelCredentialRepository;
use Illuminate\Http\Request;
use EmizorIpx\ClientFel\Services\Connection\Connection;
use Exception;
class BitacoraController extends BaseController
{
    protected $company_id;
    public function __construct(FelCredentialRepository $credential_repo)
    {
        $this->credentialrepo = $credential_repo;
        $this->company_id = 165;
    }
    public function index(Request $request)
    {
        $logs = BitacoraLog::orderBy("id","desc")->simplePaginate(30);

        return view('clientfel::bitacora', compact('logs') );
        
    }

    public function updateTokens()
    {
        $felClienttokens = FelClientToken::where("host",'like',"%sinfel.emizor.com")->get();


        foreach ($felClienttokens as $felClienttoken) {
            $connection = new Connection($felClienttoken->getHost());
        
            $clientId = $felClienttoken->getClientId();
            $clientSecret = $felClienttoken->getClientSecret();
        

            $data = [
                "grant_type" => "client_credentials",
                "client_id" => $clientId,
                "client_secret" => $clientSecret
            ];
            try {

                $response = $connection->authenticate($data);
                
                $felClienttoken->setTokenType($response['token_type']);
                $felClienttoken->setExpiresIn($response['expires_in']);
                $felClienttoken->setAccessToken($response['access_token']);
                $felClienttoken->save();
            } catch (Exception $ex) {
                \Log::debug("NO SE PUEDE AUTENTICAR LA EMPRESA # ". $felClienttoken->account_id ." con client_id : " . $clientId . " client_secret : " . $clientSecret . " con host  " . $felClienttoken->getHost());
            }
        }
        dd("done");

    }

    public function getHtmlFromInvoice($company_id, $generate_pdf = true, $use_thermal_printer =false, $return_json = false) 
    {
 
        $company = \App\Models\Company::find($company_id);

        if(empty($company)){
            return "not found";
        }

        $invoice_id = $company->invoices()->orderBy("id", "desc")->first()->id;
        $invoice = \App\Models\Invoice::with(['company','fel_invoice'])->find($invoice_id);
        
        if (empty($invoice)) {
            echo "not found";
            return;
        }
        $company = $invoice->company;
        
        $felinvoice = $invoice->fel_invoice;

        $numeroFactura = $invoice->numberFormatter();
        
        $path =$company->company_key.'/'.$invoice->hashed_id.'/invoices/'. $felinvoice->codigoSucursal."/";


        [$template, $footer_custom] = \EmizorIpx\ClientFel\Utils\TypeDocumentSector::getTemplateByDocumentSector($felinvoice->type_document_sector_id, $invoice->company_id,  $felinvoice->codigoSucursal, $use_thermal_printer, $felinvoice->typeDocument, $felinvoice->codigoPuntoVenta);

        $resourceClass = \EmizorIpx\ClientFel\Utils\TemplatesUtils::getClassResourceByDocumentSector($felinvoice->type_document_sector_id, $felinvoice->typeDocument);

        $invoice = new $resourceClass($invoice);

        $template = \Illuminate\Support\Facades\Storage::disk('template-s3')->url($template);

        $content = file_get_contents($template);

        $data = ['fiscalDocument' => $invoice->resolve(), 'fiscalDocumentOriginal' => ''];
        $render_template = \Illuminate\Support\Facades\Blade::render($content, $data);

        if ($return_json === 'true' || $return_json === true || $return_json === '1' || $return_json === 1) {
            $pdf_payload = [
                'html_pdf_url' => $render_template,
                "cuf" => $numeroFactura,
                "bucket_path" => $path . $numeroFactura,
                'bucket_name' => "no-aplicable",
                "is_html" => true
            ];
            return response()->json($pdf_payload);
        }

        if ($generate_pdf === 'true' || $generate_pdf === true || $generate_pdf === '1' || $generate_pdf === 1) {

            $pdf = (new \App\Utils\HostedPDF\NinjaPdf())->build($render_template, $path . $numeroFactura, $numeroFactura, true);

            return response($pdf)
                ->header('Content-Disposition', 'attachment; filename="' . $numeroFactura . '.pdf"')
                ->header('Content-Type', 'application/pdf');

        } else {
            return response($render_template)
                ->header('Content-Disposition', 'attachment; filename="' . $numeroFactura . '.html"')
                ->header('Content-Type', 'text/html');
        }

    }
}
