<?php
/**
 * Invoice Ninja (https://invoiceninja.com).
 *
 * @link https://github.com/invoiceninja/invoiceninja source repository
 *
 * @copyright Copyright (c) 2021. Invoice Ninja LLC (https://invoiceninja.com)
 *
 * @license https://www.elastic.co/licensing/elastic-license
 */

namespace EmizorIpx\ClientFel\Database\factories;

use App\Models\Product;
use EmizorIpx\ClientFel\Models\FelSyncProduct;
use EmizorIpx\ClientFel\Utils\Log;
use Faker\Factory;
use stdClass;

//use Faker\Generator as Faker;

class InvoiceItemFactory
{
    public static function create() : stdClass
    {
        $item = new stdClass;
        $item->quantity = 0;
        $item->cost = 0;
        $item->product_key = '';
        $item->notes = '';
        $item->discount = 0;
        $item->is_amount_discount = true;
        $item->tax_name1 = '';
        $item->tax_rate1 = 0;
        $item->tax_name2 = '';
        $item->tax_rate2 = 0;
        $item->tax_name3 = '';
        $item->tax_rate3 = 0;
        $item->sort_id = 0;
        $item->line_total = 0;
        $item->custom_value1 = '';
        $item->custom_value2 = '';
        $item->custom_value3 = '';
        $item->custom_value4 = '';
        $item->type_id = "1";

        return $item;
    }

    /**
     * Generates an array of dummy data for invoice items.
     * @param  int    $items Number of line items to create
     * @return array        array of objects
     */
    public static function generate(int $items = 1, $company_id, $user_id) :array
    {
        $faker = Factory::create();

        $data = [];

        for ($x = 0; $x < $items; $x++) {
            $product = Product::factory()->count(1)->create([
                'company_id' => $company_id,
                'user_id' => $user_id
            ])->each( function ($product) use ($company_id){
                FelSyncProduct::factory()->create([
                    'company_id' => $company_id,
                    'id_origin' => $product->id,
                    'codigo_producto' => $product->product_key,
                ]);
            })[0];


            $item = self::create();
            $item->quantity = $faker->numberBetween(1, 10);
            $item->cost = $product->cost;
            $item->line_total = $item->quantity * $item->cost;
            $item->is_amount_discount = true;
            $item->discount = 0;
            $item->notes = $faker->text(50);
            $item->product_key = $faker->word();
            $item->product_id = $product->id;
            // $item->custom_value1 = $faker->realText(10);
            // $item->custom_value2 = $faker->realText(10);
            // $item->custom_value3 = $faker->realText(10);
            // $item->custom_value4 = $faker->realText(10);
            // $item->tax_name1 = 'GST';
            // $item->tax_rate1 = 10.00;
            $item->type_id = "1";

            $data[] = $item;

            
        }

        return $data;
    }

    public static function makeDetalles($lineItems, $company_id){
        $detalles = [];
        // $total = 0;
        
        foreach($lineItems as $item){


            $product_sync = FelSyncProduct::whereIdOrigin($item->product_id)->whereCompanyId($company_id)->first();
        

            $line = new stdClass;
            $line->codigoProducto =  $item->product_key . ""; // this values was added only frontend Be careful
            $line->codigoProductoSin =  $product_sync->codigo_producto_sin ?? "" ; // this values was added only frontend Be careful
            $line->descripcion = $item->notes;
            $line->precioUnitario = $item->cost;
            $line->subTotal = round((float)$item->line_total,5);
            $line->cantidad = $item->quantity;
            $line->numeroSerie = null;

            if ($item->discount > 0)
                $line->montoDescuento = round((float)($item->cost * $item->quantity) - $item->line_total,5);

            $line->unidadMedida = $product_sync->codigo_unidad ?? "";

            $detalles[] = $line;

            // $total += $line->subTotal;
        }


        return $detalles;
    }

    
}
