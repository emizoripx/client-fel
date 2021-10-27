<?php

use EmizorIpx\ClientFel\Models\FelInvoiceRequest;
use EmizorIpx\ClientFel\Models\FelSyncProduct;


class UpdateCodigoActividadFelSyncProducts
{
    /**
     * Run the patch.
     *
     * @return void
     */
    public function run()
    {
        \Log::debug(">>>>>>>>>>>>> Actualizando códigos de actividad de fel invoice request");        
        FelInvoiceRequest::cursor()->each(function ($invoice) {
            if (strlen($invoice->codigoActividad) < 6) {
                $invoice->codigoActividad = str_pad($invoice->codigoActividad, 6, "0", STR_PAD_LEFT);
                $invoice->save();
                \Log::debug(">>>>>>>>>>>>> Código : ". $invoice->codigoActividad ." actualizado");        
            }
        });
        \Log::debug(">>>>>>>>>>>>> Termino la actualización de códigos de actividad de fel invoice request");
        \Log::debug(">>>>>>>>>>>>> Actualizando códigos de actividad de fel sync productos");        
        FelSyncProduct::cursor()->each(function ($prod) {
            if (strlen($prod->codigo_actividad_economica) < 6) {

                $prod->codigo_actividad_economica = str_pad($prod->codigo_actividad_economica, 6, "0", STR_PAD_LEFT);
                $prod->save();
                \Log::debug(">>>>>>>>>>>>> Código : " . $prod->codigo_actividad_economica . " actualizado");        
            }
        });
        \Log::debug(">>>>>>>>>>>>> Terminó la actualización de códigos de actividad de fel sync productos");
    }
}
