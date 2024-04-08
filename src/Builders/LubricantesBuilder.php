<?php

namespace EmizorIpx\ClientFel\Builders;

use EmizorIpx\ClientFel\Contracts\FelInvoiceBuilderInterface;
use EmizorIpx\ClientFel\Models\FelInvoiceRequest;
use EmizorIpx\ClientFel\Models\FelSyncProduct;
use Hashids\Hashids;
use stdClass;

class LubricantesBuilder extends BaseFelInvoiceBuilder implements FelInvoiceBuilderInterface
{
    protected $fel_invoice;

    public function __construct($data)
    {
        parent::__construct($data);
    }

    public function prepare(): FelInvoiceRequest
    {

        if ($this->source_data['update']){
            $modelFelInvoice = $this->getFelInvoiceFirst();

            if($modelFelInvoice->codigoEstado != 690){
                $this->fel_invoice = $modelFelInvoice; 
            } else{
                $this->fel_invoice = $this->getFelInvoiceFirstOrFail();
            }
            
        }
            
        else{
            
            $this->fel_invoice = new FelInvoiceRequest();}

        return $this->fel_invoice;
    }

    public function processInput(): FelInvoiceRequest
    {
        $input = array_merge(
            $this->input, ['data_specific_by_sector' => [
                    "ciudad" => $this->source_data['fel_data_parsed']['ciudad'],
                    "nombrePropietario" => $this->source_data['fel_data_parsed']['nombrePropietario'],
                    "nombreRepresentanteLegal" => $this->source_data['fel_data_parsed']['nombreRepresentanteLegal'],
                    "condicionPago" => $this->source_data['fel_data_parsed']['condicionPago'],
                    "periodoEntrega" => $this->source_data['fel_data_parsed']['periodoEntrega'],
                    ],
                    "cafc" => $this->source_data['fel_data_parsed']['cafc'],
                    "descuentoAdicional" => round($this->source_data['fel_data_parsed']['descuentoAdicional'],2),
            ],
            $this->getDetailsAndTotals()
        );
        $input['data_specific_by_sector'] = array_merge($input['data_specific_by_sector'], ['montoDeduccionIehdDS25530' => $input['montoDeduccionIehdDS25530']]);
        unset($input['montoDeduccionIehdDS25530']);
        $this->fel_invoice->fill($input);
        
        return $this->fel_invoice;
    }

    public function createOrUpdate():void
    {
        $this->fel_invoice->save();
    }

    public function getDetailsAndTotals(): array
    {
        $line_items = $this->source_data['model']->line_items;
        $model = $this->source_data['model'];

        $total = 0;

        $hashid = new Hashids(config('ninja.hash_salt'), 10);

        $montoDeduccionIehdDS25530 = 0;
        
        foreach ($line_items as $detail) {

            $id_origin = $hashid->decode($detail->product_id)[0];

            $product_sync = FelSyncProduct::whereIdOrigin($id_origin)->whereCompanyId($model->company_id)->first();

            $new = new stdClass;
            $new->codigoProducto =  $product_sync->codigo_producto  . ""; // this values was added only frontend Be careful
            $new->codigoProductoSin =  $product_sync->codigo_producto_sin . ""; // this values was added only frontend Be careful
            $new->codigoActividadEconomica =  $product_sync->codigo_actividad_economica . "";
            $new->descripcion = $detail->notes;
            $new->cantidadLitros =  $detail->cantidadLitros;
            $new->porcentajeDeduccionIehdDS25530 =  $detail->porcentajeDeduccionIehdDS25530;
            $new->precioUnitario = $detail->cost;
            $new->subTotal = round((float)$detail->line_total,2);
            $new->cantidad = $detail->quantity;

            if ($detail->discount > 0)
                $new->montoDescuento = round((float)($detail->cost * $detail->quantity) - $detail->line_total,2);

            $new->unidadMedida = $product_sync->codigo_unidad;

            if (is_numeric($new->cantidadLitros) && $new->cantidadLitros > 0  && is_numeric($new->porcentajeDeduccionIehdDS25530) && $new->porcentajeDeduccionIehdDS25530 > 0 ) {
                $montoDeduccionIehdDS25530 += $new->cantidadLitros * $new->porcentajeDeduccionIehdDS25530;
            }
            
            $details[] = $new;

            $total += $new->subTotal;
        }

        $total = $total - round($this->source_data['fel_data_parsed']['descuentoAdicional'], 2);


        return [
            "tipoCambio" => $this->source_data['fel_data_parsed']['tipo_cambio'],
            "montoTotal" => $total,
            "montoTotalMoneda" => round($total / $this->source_data['fel_data_parsed']['tipo_cambio'],2),
            "montoDeduccionIehdDS25530" => $montoDeduccionIehdDS25530,
            "montoTotalSujetoIva" => round($total- $montoDeduccionIehdDS25530, 2),
            "detalles" => $details,

        ];
    }

    public function getFelInvoice(): FelInvoiceRequest
    {
        return $this->fel_invoice;
    }
}
