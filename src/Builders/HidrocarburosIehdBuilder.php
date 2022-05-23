<?php

namespace EmizorIpx\ClientFel\Builders;

use EmizorIpx\ClientFel\Contracts\FelInvoiceBuilderInterface;
use EmizorIpx\ClientFel\Models\FelInvoiceRequest;
use EmizorIpx\ClientFel\Models\FelSyncProduct;
use Hashids\Hashids;
use stdClass;

class HidrocarburosIehdBuilder extends BaseFelInvoiceBuilder implements FelInvoiceBuilderInterface
{
    protected $fel_invoice;

    public function __construct($data)
    {
        parent::__construct($data);
    }

    public function prepare(): FelInvoiceRequest
    {
        if ($this->source_data['update']){
            $modelFelInvoice = FelInvoiceRequest::whereIdOrigin($this->source_data['model']->id)->first();

            if($modelFelInvoice->codigoEstado != 690){
                $this->fel_invoice = $modelFelInvoice; 
            } else{
                $this->fel_invoice = FelInvoiceRequest::whereIdOrigin($this->source_data['model']->id)->firstOrFail();
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
            ],
            $this->getDetailsAndTotals()
        );

        $input ['data_specific_by_sector'] = array_merge( $input ['data_specific_by_sector'], [ 'montoIehd' => $input['montoIehd'] ] );

        \Log::debug("Data Document: " , [$input ['data_specific_by_sector']]);

        unset( $input['montoIehd'] );

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

        $montoIedh = 0;

        \Log::debug("Line Items: " , [$line_items]);

        $hashid = new Hashids(config('ninja.hash_salt'), 10);

        foreach ($line_items as $detail) {

            $id_origin = $hashid->decode($detail->product_id)[0];

            $product_sync = FelSyncProduct::whereIdOrigin($id_origin)->whereCompanyId($model->company_id)->first();

            $new = new stdClass;
            $new->codigoProducto =  $product_sync->codigo_producto  . ""; // this values was added only frontend Be careful
            $new->codigoProductoSin =  $product_sync->codigo_producto_sin . ""; // this values was added only frontend Be careful
            $new->codigoActividadEconomica =  $product_sync->codigo_actividad_economica . "";
            $new->porcentajeIehd =  $detail->porcentajeIehd;
            $new->descripcion = $detail->notes;
            $new->precioUnitario = $detail->cost;
            $new->subTotal = round((float)$detail->line_total,2);
            $new->cantidad = $detail->quantity;

            if ($detail->discount > 0)
                $new->montoDescuento = round((float)($detail->cost * $detail->quantity) - $detail->line_total,2);

            $new->unidadMedida = $product_sync->codigo_unidad;

            $details[] = $new;

            $total += $new->subTotal;
            $montoIedh += ($new->subTotal * $new->porcentajeIehd);
        }

        $total = round($total +  $montoIedh, 2) - round($this->source_data['fel_data_parsed']['descuentoAdicional'], 2) ;

        \Log::debug("Monto IEHD >>>>>>>>>>>>>>>>>>>>>>>> " . $montoIedh);

        return [
            "tipoCambio" => $this->source_data['fel_data_parsed']['tipo_cambio'],
            "montoTotal" => $total,
            "montoTotalMoneda" => round($total / $this->source_data['fel_data_parsed']['tipo_cambio'],2),
            "montoIehd" => round( $montoIedh, 2 ),
            "montoTotalSujetoIva" => $total,
            "detalles" => $details
        ];
    }

    public function getFelInvoice(): FelInvoiceRequest
    {
        return $this->fel_invoice;
    }
}
