<?php

namespace EmizorIpx\ClientFel\Traits;

use EmizorIpx\ClientFel\Services\Terrasur\TerrasurService;

trait ReceiptTrait {


    public function convertReceipt()
    {

        $data = request()->only(['document_data', 'document_search']);
        $this->invoice->document_data =  $data['document_data'];
        $this->invoice->document_search=  $data['document_search'];
        $this->invoice->document_type =  'receipt';
        $this->invoice->custom_value1 =  $data["recibo_ticket"];
        return $this->invoice;

    }

    public function conciliateReceiptPayment()
    {
        $data = $this->invoice->document_data;
        
        if (empty($data)){
            throw new \Exception("No tiene el formato correcto ". $this->invoice->id);
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
                    "recibo_num" => intval($this->invoice->number),
                    "usuario_sucursal" => $this->invoice->user->name()
            ];
        }else {
            $bbr_pago = $bbr_client->bbr_servicio->bbr_pago_servicio;
            
            $input = [
                "factura_bs_monto" => "0",
                "factura_cliente_nit" => "0",
                "factura_cliente_nombre" => "0",
                "factura_codigo_control" => "0",
                "factura_num" => "0",
                "recibo_num" => intval($this->invoice->number),
                "numero_contrato" => $bbr_pago->num_contrato,
                "recibo_efectivo_sus" => $bbr_pago->monto_pago,
                "recibo_efectivo_bs" => 0.0,
                "usuario_sucursal" => $this->invoice->user->name(),
                "facturar"=> $bbr_client->bbr_servicio->facturar,
                "concepto"=> $bbr_pago->concepto,
                "id_contrato"=> $bbr_pago->id_contrato,
                "num_unidades"=> $bbr_pago->unidades,
                "id_servicio"=> $bbr_client->bbr_servicio->id_servicio ?? 0,
                "precio_total"=> $bbr_pago->valor_unit_bs,
                "precio_unidad"=> $bbr_pago->valor_unit_bs,
            ];
        }
        
        if (isset($this->invoice->fel_invoice)) {
            $inv = $this->invoice->fel_invoice;
            $input["factura_bs_monto"] = $inv->montoTotal;
            $input["factura_cliente_nit"] = $inv->numeroDocumento;
            $input["factura_cliente_nombre"] = $inv->nombreRazonSocial;
            $input["factura_codigo_control"] = $inv->cuf;
            $input["factura_num"] = $inv->numeroFactura ;
        }


        $terrasur = new TerrasurService($input);
        return $terrasur->conciliation(); 
    }

}