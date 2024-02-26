<?php
namespace EmizorIpx\ClientFel\Services\Terrasur;


use App\Models\Invoice;
use Illuminate\Support\Facades\Blade;
use Beganovich\Snappdf\Snappdf;

class TerrasurService {

    protected $conexion;

    protected $data;

    public function __construct($input)
    {
        $this->conexion = new TerrasurConnect();
        $this->commonData();
        $this->data = array_merge($this->data, $input);
    }

    public function commonData()
    {
        $this->data = [
            "usuario_sucursal" => "emizor",
            "version_app_mp3" => "v1.5.47",
            "control_code_service" => env("TERRASUR_CODE_SERVICE", NULL),
            "empresa" => 3,
            "imei" => "353007061023451,353007061023469",
        ];
    }

    public function conciliation()
    {
        $this->conexion->conciliate($this->data);
    }    

    public function listPaymentTypes()
    {
        
        if ($this->data['paymentType'] == "services")
            $this->conexion->getPaymentTypeService($this->data);
        else
            $this->conexion->getPaymentTypeQuote($this->data);
    
    }

    public function listPayments()
    {
        if ($this->data['paymentType'] == "services")
            return $this->conexion->getPaymentsServices($this->data);

        return $this->conexion->getPaymentsQuota($this->data);
    
    }
    public function search()
    {
        if ($this->data['entity'] == "contract")
            return $this->conexion->searchContract($this->data);

        return $this->conexion->searchClient($this->data);
    
    }
    
    public function getReceiptPdf($id)
    {
        $invoice = Invoice::find($id);

        if (!$invoice) {
            return response()->json([
                "success" => false,
                "msg" => "Registro inexistente"
            ]);
        }
        $companyLogo = isset($invoice->company) ? 'data:image/jpg;base64,' . base64_encode(file_get_contents($invoice->company->present()->logo())) : '';
     
        $content = file_get_contents("https://emizorv5.s3.amazonaws.com/receipt_template.blade.php");
        // $content = file_get_contents(public_path("tmp/receipt_template.blade.php")); // local

        $html = Blade::render($content, ['invoice' => $invoice,"companyLogo" => $companyLogo]);

        $pdf = new Snappdf();

        $generated = $pdf->setHtml(str_replace('%24', '$', $html))->generate();

        return $generated;
    }

    public function getResponse()
    {
        return $this->conexion->getResponse();
    }

}