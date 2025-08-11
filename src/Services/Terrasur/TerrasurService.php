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
            "empresa" => auth()->user()->company()->settings->custom_value1,
            "imei" => "353007061023451,353007061023469",
        ];
    }

    public function conciliation($id)
    {
        $invoice = Invoice::find($id);

        if (!$invoice) {
            return response()->json([
                "success" => false,
                "msg" => "Registro inexistente"
            ]);
        }

        $data = $invoice->document_data;
        
        if (empty($data)){
            throw new \Exception("No tiene el formato correcto ". $invoice->id);
        }
        $bbr_client = $data->bbr_cliente;
        
        info("bbr_client " . "type : " . gettype($bbr_client) . " contiene :" . json_encode($bbr_client));
        if(isset($bbr_client->bbr_tipo_pagos)) {
            $bbr_tipo_pagos = $bbr_client->bbr_tipo_pagos;
            $bbr_pago = $bbr_tipo_pagos[0]->bbr_pagos[0];
            $input = [
                    "factura_bs_monto" => "0",
                    "factura_cliente_nit" => "0",
                    "factura_cliente_nombre" => "0",
                    "factura_codigo_control" => "0",
                    "factura_num" => "0",
                    "id_pago" => $bbr_pago->id_pago,
                    "monto_pago" => $bbr_pago->monto_pago,
                    "numero_contrato" => $bbr_pago->num_contrato,
                    "recibo_efectivo_bs" => 0.0,
                    "recibo_efectivo_sus" => $bbr_pago->monto_pago,
                    "recibo_num" => intval($invoice->number),
                    "usuario_sucursal" => $invoice->user->name()
            ];
        }else {
            $bbr_pago = $bbr_client->bbr_servicio->bbr_pago_servicio;
            
            $input = [
                "factura_bs_monto" => "0",
                "factura_cliente_nit" => "0",
                "factura_cliente_nombre" => "0",
                "factura_codigo_control" => "0",
                "factura_num" => "0",
                "recibo_num" => intval($invoice->number),
                "numero_contrato" => $bbr_client->num_contrato,
                "recibo_efectivo_sus" => $bbr_pago->valor_unit_sus,
                "recibo_efectivo_bs" => 0.0,
                "usuario_sucursal" => $invoice->user->name(),
                "facturar"=> $bbr_client->bbr_servicio->facturar,
                "concepto"=> $bbr_pago->concepto,
                "id_contrato"=> $bbr_pago->id_contrato,
                "num_unidades"=> $bbr_pago->unidades,
                "id_servicio"=> $bbr_client->bbr_servicio->id_servicio ?? 0,
                "precio_total"=> $bbr_pago->valor_unit_bs,
                "precio_unidad"=> $bbr_pago->valor_unit_bs,
            ];
        }
        
        if (isset($invoice->fel_invoice)) {
            $inv = $invoice->fel_invoice;
            $input["factura_bs_monto"] = $inv->montoTotal;
            $input["factura_cliente_nit"] = $inv->numeroDocumento;
            $input["factura_cliente_nombre"] = $inv->nombreRazonSocial;
            $input["factura_codigo_control"] = $inv->cuf;
            $input["factura_num"] = $inv->numeroFactura ;
        }

        $data = array_merge($this->data, $input);

        $this->conexion->conciliate($data);

        return $this->conexion->getResponse();
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
    // public function listReceipts()
    // {
    //     $invoices = Invoice::where("company_id", auth()->user()->company()->id)
    //         ->where("document_type", "receipt")
    //         ->whereNotNull("document_data")
    //         ->select('id', 'document_data', 'number', 'created_by')
    //         ->paginate(30); // <-- Esto usa paginación directa desde la BD

    //         // Transformamos solo los elementos de esta página
    //         $invoices->getCollection()->transform(function ($inv) {

    //         if (is_null($inv->document_data)) {
    //             return null;
    //         }

    //         $additional_parameters = $inv->document_data;
    //         $bbr_cliente = $additional_parameters->bbr_cliente ?? null;

    //         if (!$bbr_cliente) return null;

    //         $bbr_tipo_pago = $bbr_cliente->bbr_tipo_pagos[0] ?? null;

    //         if ($bbr_tipo_pago && isset($bbr_tipo_pago->bbr_pagos[0])) {
    //             $bbr_pago = $bbr_tipo_pago->bbr_pagos[0];
    //         } else {
    //             $bbr_servicio = $bbr_cliente->bbr_servicio->bbr_pago_servicio ?? null;
    //             if (!$bbr_servicio) return null;

    //             // Se recomienda usar fecha actual solo si no tienes otra
    //             $fecha = now(); // <- Reemplaza por lógica real si aplica

    //             $bbr_pago = (object) [
    //                 'fecha_pago' => $fecha->format('d/m/Y'),
    //                 'moneda' => 1,
    //                 'monto_pago' => $bbr_servicio->unidades_seleccionada * $bbr_servicio->valor_unit_sus,
    //                 'num_pago' => "Servicio",
    //                 'concepto_pago' => $bbr_servicio->concepto,
    //             ];
    //         }

    //         return [
    //             'id' => $inv->id,
    //             'number' => $inv->number,
    //             'created_by' => $inv->created_by,
    //             'num_contrato' => $bbr_cliente->num_contrato ?? null,
    //             'client_name' => $bbr_cliente->nombre_cliente ?? null,
    //             'fecha_pago' => $bbr_pago->fecha_pago ?? null,
    //             'num_pago' => $bbr_pago->num_pago ?? null,
    //             'concepto_pago' => $bbr_pago->concepto_pago ?? null,
    //         ];
    //     });

    //     // El objeto paginado se devuelve ya transformado
    //     return $invoices;
    // }

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