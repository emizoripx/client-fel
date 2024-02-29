<?php
namespace EmizorIpx\ClientFel\Services\Cobrosqr;


use App\Repositories\InvoiceRepository;
use App\Factory\InvoiceFactory;
use App\Models\Product;
use App\Utils\Traits\MakesHash;
use App\Models\Client;
use App\Models\Invoice;
use Illuminate\Support\Facades\Storage;
use App\Services\Invoice\SendEmail;
use EmizorIpx\ClientFel\Models\FelInvoiceRequest;

class CobrosqrService{

    use MakesHash;
    /**
     * @var InvoiceRepository
     */
    protected $invoice_repo;

    public function __construct()
    {
        $this->invoice_repo = new InvoiceRepository();
    }

    public function createInvoice($data)
    {
        
        $obj =  \DB::table("cobros_qr_links")->where("imei", $data["imei"])->first();
        if (!empty($obj)) {
            if ($this->getVendisCompany() !== null) {
                if ( ($invoice_id = $this->invoiceWasCreated($data["ticket"]) ) == -1 ) {
                    info("building invoice");

                    $invoice = $this->buildInvoice($obj->client_id,$data);
                    info("mark send invoice");
                    $invoice->service()->markSent()->touchPdf()->save();
                    info("send email");
    
                    $send_email = new SendEmail($invoice, 'custom1');
                    $send_email->run();
                    info("CREATING.....");
                    $this->callbackResponseInvoice($invoice->id, $data['imei']);
                } else {
                    info("VOLVIENDO A ENVIAR EL CALLBACK.....");
                    $this->callbackResponseInvoice($invoice_id, $data['imei']);
                }   


            }
        } else {
            //send email , not register imei
            logger()->error("Error-COBROS-QR-COMMISSION: enviado : " . json_encode($data) );

        }
    }

    private function invoiceWasCreated($ticket)
    {
        $invoice = FelInvoiceRequest::whereCompanyId($this->getVendisCompany())->whereFacturaTicket($ticket)->select("id","id_origin")->first();

        if (!empty($invoice)) {
            return $invoice->id_origin;
        }

        return -1;
    }

    private function callbackResponseInvoice($invoice_id, $imei)
    {
        dispatch(function () use ($invoice_id, $imei){
            sleep(10);
            $invoice = Invoice::find($invoice_id);

            if ($invoice) {
                try {
                    $invoice->load('fel_invoice');
                    $pdf_url = $invoice->service()->getInvoicePdf();
                    info("pdf_url  " . $pdf_url);
                    // Aquí deberías obtener los datos necesarios del objeto $invoice->fel_invoice
                    $dataToSend = [
                        "cuf" => $invoice->fel_invoice->cuf,
                        "pdf_url" => is_null($pdf_url) ? null : Storage::url($pdf_url),
                        "imei" => $imei,
                        "emission_date" => $invoice->fel_invoice->fechaEmision,
                        "document_number" => $invoice->fel_invoice->numeroDocumento,
                        "business_name" => $invoice->fel_invoice->nombreRazonSocial,
                        "ticket" => $invoice->fel_invoice->factura_ticket
                    ];
                    info("sending data : " ,$dataToSend);
                    // Lógica para enviar los datos a través de Guzzle
                    $client = new \GuzzleHttp\Client();
                    $response = $client->post('https://marcus.requestcatcher.com/test', [
                        'json' => $dataToSend
                    ]);

                    // Manejar la respuesta del endpoint
                    $responseBody = json_decode($response->getBody(), true);
                    info("response " , [$responseBody]);
                // if ($responseBody['success']) {
                //     // Lógica en caso de éxito
                //     // Por ejemplo, registrar el éxito en el registro de la aplicación
                // } else {
                //     // Lógica en caso de fallo
                //     // Por ejemplo, registrar el fallo en el registro de la aplicación
                // }
                } catch (\Throwable $th) {
                    logger()->emergency("Error: ". $th->getMessage()." File: " .$th->getFile(). " Line: ". $th->getLine());
                }
               
            }

        })->afterResponse();
    }

    private static function getVendisCompany()
    {
        return env("VENDIS_COMPANY_ID", null);
    }

    private static function getVendisUser()
    {
        return env("VENDIS_USER_ID", null);
    }


    private function getProductVendis(): array
    {
        $product_id = env("VENDIS_PRODUCT_ID", null);

        return Product::find($product_id)->toArray();
    }

    private function buildInvoice($client_id, $data)
    {

        $product = $this->getProductVendis();
        if (!empty($data["montoTotal"])) {

            $product = array_merge ($this->getProductVendis(),[
                        "cost" => $data["montoTotal"]
                    ]);
        }
        
        $input = [
            "client_id" => $client_id,
            "date" =>  now()->format("Y-m-d"),
            "line_items" => [
                (object)[
                    "product_key"=> $product["product_key"],
                    "notes"=> $product["notes"],
                    "product_id"=> $this->encodePrimaryKey($product['id']),
                    "cost"=> $product["cost"],
                    "quantity"=> 1
                ]
            ],
            "felData"  => [
                "sector_document_type_id" =>  1,
                "montoTotal" =>  $product["cost"],
                "codigoMetodoPago" =>  1,
                "montoTotalMoneda" =>  $product["cost"],
                "montoTotalSujetoIva" =>  $product["cost"],
                "codigoMoneda" =>  1, // BOLIVIAN
                "nombreRazonSocial" => $data['nombreRazonSocial']??null,
                "numeroDocumento" => $data['numeroDocumento']??null,
                "codigoTipoDocumentoIdentidad" => $data["codigoTipoDocumentoIdentidad"]??null,
                "facturaTicket" => $data["ticket"],
                "typeDocument"=>0,
            ],
            "should_emit" => 'true'
        ];

        $request = request();
        $request->replace($input);

        $invoice = $this->invoice_repo->save($input, InvoiceFactory::create($this->getVendisCompany(), $this->getVendisUser()));

        return $invoice;
        
        
    }

    public static function registerNewImeis(Client $client)
    {
        
        if (static::getVendisCompany() == $client->company_id) {
            info("REGISTER-IMEIS ===> ingresa");
            $imeis = $client->custom_value4;
            $imeis_array_input = explode( ",", $imeis);

            $imeis_client = \DB::table("cobros_qr_links")->whereClientId($client->id)->select("imei")->pluck("imei")->toArray();
            
            $imeis_to_delete = array_diff($imeis_client, $imeis_array_input);
            info("imeis to delete ========> " , $imeis_to_delete);
            if (!empty($imeis_to_delete))
                \DB::table("cobros_qr_links")->whereClientId($client->id)->whereIn("imei",$imeis_to_delete)->delete();

            $imeis_not_register = array_diff($imeis_array_input, $imeis_client);
            
            $input = [];
            
            $uniques = [];
            foreach ($imeis_not_register as $imei) {
                $unique = $client->id."-".$imei;
                if (!in_array($unique, $uniques)) {
                
                    $input [] = [
                        "client_id" => $client->id,
                        "imei"=> $imei,
                        "created_at"=>now()->format("Y-m-d H:i:s"),
                        "updated_at"=>now()->format("Y-m-d H:i:s")
                    ];
                    $uniques[] = $unique;
                }
            }
            \DB::table("cobros_qr_links")->insert($input);

        }
    }

    public function unlinkImei($imei)
    {
        \DB::table("cobros_qr_links")->whereImei($imei)->delete();
    }

}