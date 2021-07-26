<?php

use EmizorIpx\ClientFel\Models\FelInvoiceRequest;

class UpdateIdOriginFacturaNotasCreditoDebito
{
    /**
     * Run the patch.
     *
     * @return void
     */
    public function run()
    {

        $ids_factura_original = FelInvoiceRequest::whereNotNull('factura_original_id')->pluck('factura_original_id');

        $data = FelInvoiceRequest::whereIn('id',$ids_factura_original)->pluck('id_origin','id');

        $datos = FelInvoiceRequest::whereNotNull('factura_original_id')->get();

        foreach ($datos as $d) {
            
            $d->factura_original_id = $data[$d->factura_original_id] ?? $d->factura_original_id;
            $d->save();
        }
        
    }
}
