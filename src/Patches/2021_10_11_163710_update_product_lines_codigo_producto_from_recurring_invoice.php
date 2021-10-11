<?php

use EmizorIpx\ClientFel\Models\FelSyncProduct;
use Hashids\Hashids;

class UpdateProductLinesCodigoProductoFromRecurringInvoice
{
    /**
     * Run the patch.
     *
     * @return void
     */
    public function run()
    {
        $hashid = new Hashids(config('ninja.hash_salt'), 10);

        \App\Models\Company::select('id')->cursor()->each(function ($company) use ($hashid) {

            $counterInvoicesOk = 0;
            $counterInvoicesNotOk = 0;
            $invoiceWithProblems = [];

            $products = FelSyncProduct::where('COMPANY_ID', $company->id)->pluck('codigo_producto', 'id_origin');
            \Log::debug("=====================company id : $company->id ===================");

            $products_encoded = [];

            foreach ($products as $key => $value) {

                $id_encoded =  $hashid->encode($key);

                $products_encoded[$id_encoded] = $value;
            }

            $init = microtime(true);
            \App\Models\RecurringInvoice::whereCompanyId($company->id)->withTrashed()->cursor()->each(function ($invoice) use ($products_encoded, &$counterInvoicesOk, &$counterInvoicesNotOk, &$invoiceWithProblems) {



                $line_items = $invoice->line_items;
                $updated_line_items = [];
                $has_problem = false;
                foreach ($line_items as $line_item) {

                    $clone_line_item = $this->cloneObject($line_item);
                    if (isset($line_item->product_id)) {

                        $clone_line_item->codigo_producto = isset($products_encoded[$line_item->product_id]) ? $products_encoded[$line_item->product_id] : "";
                        // \Log::debug(json_encode($clone_line_item));

                        $updated_line_items[] = $clone_line_item;
                    } else {
                        $has_problem = true;
                        $updated_line_items[] = $clone_line_item;
                    }
                }

                $invoice->line_items = $updated_line_items;

                $invoice->save();

                if ($has_problem) {
                    $counterInvoicesNotOk++;
                    $invoiceWithProblems[] = $invoice->id;
                } else {
                    $counterInvoicesOk++;
                }
            });
            \Log::warning(" \n\t\t\t\t\tCompañia  #$company->id  tiene: \n\t\t\t\t\t\t$counterInvoicesOk facturas recurrentes actualizadas \n\t\t\t\t\t\t$counterInvoicesNotOk facturas con problemas : " . json_encode($invoiceWithProblems));
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
