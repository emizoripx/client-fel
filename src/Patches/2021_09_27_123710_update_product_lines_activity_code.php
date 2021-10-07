<?php

use EmizorIpx\ClientFel\Models\FelInvoiceRequest;
use EmizorIpx\ClientFel\Models\FelSyncProduct;

class UpdateProductLinesActivityCode
{
    /**
     * Run the patch.
     *
     * @return void
     */
    public function run()
    {

        \App\Models\Company::select('id')->cursor()->each(function ($company) {

            $counterInvoicesOk = 0;
            $counterInvoicesNotOk = 0;
            $invoiceWithProblems = [];

            $fel_products = FelSyncProduct::where('company_id', $company->id)->withTrashed()->pluck('codigo_actividad_economica', 'codigo_producto');
            $fel_products_ids = FelSyncProduct::where('company_id', $company->id)->withTrashed()->pluck('codigo_actividad_economica', 'id_origin');
            $products = \App\Models\Product::where('company_id', $company->id)->withTrashed()->pluck('id', 'product_key');
        
            \Log::debug("products " . json_encode($products));
            \Log::debug("fel_products " . json_encode($fel_products));
            \Log::debug("fel_products_id " . json_encode($fel_products_ids));

            $init = microtime(true);
            FelInvoiceRequest::whereCompanyId($company->id)->cursor()->each(function ($invoice) use ($products,$fel_products, $fel_products_ids, &$counterInvoicesOk, &$counterInvoicesNotOk, &$invoiceWithProblems) {


                
                $line_items = $invoice->detalles;
                if ( is_null($line_items) ){
                    \Log::debug("FEL INVOICE REQUEST ID " . $invoice->id . " with detalles NULL");
                }else {

                    $updated_line_items = [];
                    $has_problem = false;
                    foreach ($line_items as $line_item) {
                        $clone_line_item = $this->cloneObject($line_item);
                        if ( is_null ($clone_line_item)) {
                            \Log::debug("FEL INVOICE REQUEST ID " . $invoice->id . " with detalles NULL");
                            continue;
                        }

                        
                        if (isset($clone_line_item->codigoProducto)) {
    
                            $clone_line_item->codigoActividadEconomica = isset($fel_products[$clone_line_item->codigoProducto]) ? $fel_products[$clone_line_item->codigoProducto] : "";

                            if (empty($clone_line_item->codigoActividadEconomica)){

                                $id_origin = isset($products[$clone_line_item->codigoProducto]) ? $products[$clone_line_item->codigoProducto] : "";
                                
                                \Log::debug("codigos_actividad -> id origin " . $id_origin . " de " . $clone_line_item->codigoProducto );
                                if (!is_null($id_origin)) {

                                    $clone_line_item->codigoActividadEconomica = isset($fel_products_ids[$id_origin]) ? $fel_products_ids[$id_origin] : "";
                                    \Log::debug("codigos_actividad encontrado " . $clone_line_item->codigoActividadEconomica);
                                }
                            }
                                
                                
                            $updated_line_items[] = $clone_line_item;
                        } else {
                            \Log::debug("codigo producto : " . json_encode($clone_line_item));
                            $has_problem = true;
                            $updated_line_items[] = $clone_line_item;
                        }
                    }
                    
                    $invoice->detalles = $updated_line_items;
    
                    $invoice->save();
    
                    if ($has_problem) {
                        $counterInvoicesNotOk++;
                        $invoiceWithProblems[] = $invoice->id;
                    } else {
                        $counterInvoicesOk++;
                    }
                }
            });
            \Log::warning(" \n\t\t\t\t\tCompañia  #$company->id  tiene: \n\t\t\t\t\t\t$counterInvoicesOk facturas felinvoicerequest actualizadas \n\t\t\t\t\t\t$counterInvoicesNotOk facturas con problemas : " . json_encode($invoiceWithProblems));
            \Log::debug("=====================COMPLETADO EN : " . (microtime(true) - $init) . " ===================");
        });
    }

    public function cloneObject($object)
    {
        $clone_object = new \stdClass;

        foreach ($object as $key => $value) {
            $clone_object->{$key} = $value;
        }


        return $clone_object;
    }
}
