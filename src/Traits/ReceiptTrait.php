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
        if(!is_null($bbr_tipo_pagos = $bbr_client->bbr_tipo_pagos)) {
            $bbr_pago = $bbr_tipo_pagos[0]->bbr_pagos[0];
        }else {
            $bbr_pago = $bbr_client->bbr_servicio->bbr_pagos_servicios[0];
        }
        
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
        // if fel invoice was created 
        if (isset($this->invoice->fel_invoice)) {
            $inv = $this->invoice->fel_invoice;
            $input["factura_bs_monto"] = $inv->montoTotal;
            $input["factura_cliente_nit"] = $inv->numeroDocumento;
            $input["factura_cliente_nombre"] = $inv->nombreRazonSocial;
            $input["factura_codigo_control"] = $inv->cuf;
            $input["factura_num"] = $inv->numeroFactura ;
        }


        $terrasur = new TerrasurService($input);
        $terrasur->conciliation(); 
    }
    // 1 BBR
    // 2 RENACER
    // 3 TERRASUR (EN ESTE CASO SOLO TERRASUR)
    // MONEDA = 1 ES dolares 
    // MONEDA = 2 ES BOLIVIANOS

}