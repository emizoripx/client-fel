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
        $tsm = "STORE>CREATE-INVOICE #" . $data['ticket']. " [imei='".$data["imei"]."']>>>" ;

        cobrosqr_logging( $tsm . "start");
        $obj =  \DB::table("cobros_qr_links")->where("imei", trim($data["imei"]))->first();
        if (!empty($obj)) {
            if ($this->getVendisCompany() !== null) {
                if ( ($invoice_id = $this->invoiceWasCreated($data["ticket"]) ) == -1 ) {
                    cobrosqr_logging($tsm . "building invoice ");
                    $invoice = $this->buildInvoice($obj->client_id,$data);

                    cobrosqr_logging($tsm . "sending email to: " . $invoice->invitation?->contact->email);
                    $invoice->service()->markSent()->touchPdf()->save();
                    $send_email = new SendEmail($invoice, 'custom1');
                    $send_email->run();
                    cobrosqr_logging($tsm . "marked as paid");
                    $invoice->service()->markPaid();
                    cobrosqr_logging($tsm . "Invoice created : sending callback ");
                    $this->callbackResponseInvoice($invoice->id, $data['imei'], $data["ticket"]);
                } else {
                    cobrosqr_logging($tsm . "Invoice was already created : sending callback ");
                    $this->callbackResponseInvoice($invoice_id, $data['imei'], $data["ticket"]);
                }   


            }else {
                cobrosqr_logging($tsm . "CREDENTIALS NOT FOUND: ");
            }
        } else {
            cobrosqr_logging($tsm . "NOT-FOUND: ");
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

    private function callbackResponseInvoice($invoice_id, $imei, $ticket)
    {
        $vendis_call_back_url = $this->getVendisCallbackUrl();
        $tsm = "STORE>CALLBACK #" . $ticket . " [imei='" . $imei . "']>>>";

        if (empty($vendis_call_back_url)){
            cobrosqr_logging($tsm . "NOT FOUND CALLBACK URL");
            return;
        }
        

        dispatch(function () use ($invoice_id, $imei, $vendis_call_back_url, $tsm){

            cobrosqr_logging($tsm . "waiting");
            sleep(10); //sleep 10 seconds for waiting invoice process
            cobrosqr_logging($tsm . "processing");
            
            $invoice = Invoice::find($invoice_id);

            if (empty($invoice)) {
                cobrosqr_logging($tsm . "NOT FOUND invoice #" . $invoice_id);
                return;
            }

            try {
                $invoice->load('fel_invoice');
                cobrosqr_logging($tsm . "load fel invoice successfully ");
                $pdf_url = $invoice->service()->getInvoicePdf();
                cobrosqr_logging($tsm . "pdf_url generated : " . $pdf_url);

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
                
                cobrosqr_logging($tsm . " SEND TO [".$vendis_call_back_url."] data : " , $dataToSend);
                // Lógica para enviar los datos a través de Guzzle
                $client = new \GuzzleHttp\Client();
                $response = $client->post($vendis_call_back_url, [
                    'json' => $dataToSend
                ]);

                // Manejar la respuesta del endpoint
                $responseBody = json_decode($response->getBody(), true);


                if ( isset($responseBody['success']) && $responseBody['success'] == true) {
                    cobrosqr_logging($tsm . "RESPONSE:SUCCESS");
                } else {
                    cobrosqr_logging($tsm . "RESPONSE:FAIL");
                    
                }
            } catch (\Throwable $th) {
                cobrosqr_logging($tsm ."ERROR: " . $th->getMessage() . " File: " . $th->getFile() . " Line: " . $th->getLine());
            }

        })->afterResponse();
    }

    private static function getVendisCallbackUrl()
    {
        return env("VENDIS_CALLBACK_URL", null);
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
                "codigoException" =>  1,
                "facturaTicket" => $data["ticket"],
                "typeDocument"=>0,
            ],
            "should_emit" => 'true'
        ];
        $tsm = "STORE>BUILD-INVOICE #" . $data["ticket"] . " [imei='" . $data['imei'] . "']>>>";
        $request = request();
        $request->replace($input);
        
        cobrosqr_logging($tsm . "INPUT-INVOICE: ". json_encode($input));

        $invoice = $this->invoice_repo->save($input, InvoiceFactory::create($this->getVendisCompany(), $this->getVendisUser()));
        cobrosqr_logging($tsm . "INVOICE CREATED SUCCESS ");
        return $invoice;
        
        
    }

    public static function registerNewImeis(Client $client)
    {

        if (static::getVendisCompany() == $client->company_id) {
            $tsm = "REGISTER-IMEI CLIENT= ".$client->id.">>>>";
            $imeis = $client->custom_value4;
            $imeis_array_input = explode( ",", $imeis);
            //cleaning strings with spaces
            $imeis_array_input = array_map('trim', $imeis_array_input);
            
            cobrosqr_logging($tsm . "CLIENT_NAME=".$client->name." IMEIS=".$imeis);

            $imeis_client = \DB::table("cobros_qr_links")->whereClientId($client->id)->select("imei")->pluck("imei")->toArray();
            
            $imeis_to_delete = array_diff($imeis_client, $imeis_array_input);
            
            cobrosqr_logging($tsm . "imeis-for-delete=" , $imeis_to_delete);

            if (!empty($imeis_to_delete))
                \DB::table("cobros_qr_links")->whereClientId($client->id)->whereIn("imei",$imeis_to_delete)->delete();

            $imeis_not_register = array_diff($imeis_array_input, $imeis_client);
            cobrosqr_logging($tsm . "imeis-for-create=", $imeis_not_register);
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