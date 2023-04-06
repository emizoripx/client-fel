<?php

namespace EmizorIpx\ClientFel\Builders;

use EmizorIpx\ClientFel\Contracts\FelInvoiceBuilderInterface;
use EmizorIpx\ClientFel\Models\FelInvoiceRequest;
use EmizorIpx\ClientFel\Models\FelSyncProduct;
use Hashids\Hashids;
use stdClass;

class AlcanzadaIceBuilder extends BaseFelInvoiceBuilder implements FelInvoiceBuilderInterface
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
            $this->input,[
                "montoGiftCard" => round($this->source_data['fel_data_parsed']['montoGiftCard'],2),
                "descuentoAdicional" => round($this->source_data['fel_data_parsed']['descuentoAdicional'],2),
                "cafc" => $this->source_data['fel_data_parsed']['cafc'],
                "data_specific_by_sector" => [
                    "montoIceEspecifico" => round($this->source_data['fel_data_parsed']['montoIceEspecifico'],2),
                    "montoIcePorcentual" => round($this->source_data['fel_data_parsed']['montoIcePorcentual'],2),
                ]
            ],
            $this->getDetailsAndTotals()
        );

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

        foreach ($line_items as $detail) {

            $id_origin = $hashid->decode($detail->product_id)[0];

            $product_sync = FelSyncProduct::whereIdOrigin($id_origin)->whereCompanyId($model->company_id)->first();

            \Log::debug("Detail: " . json_encode($detail));

            $new = new stdClass;
            $new->codigoProducto =  $product_sync->codigo_producto  . ""; // this values was added only frontend Be careful
            $new->codigoProductoSin =  $product_sync->codigo_producto_sin . ""; // this values was added only frontend Be careful
            $new->codigoActividadEconomica =  $product_sync->codigo_actividad_economica . "";
            $new->descripcion = $detail->notes;
            $new->precioUnitario = $detail->cost;
            $new->subTotal = round((float)$detail->line_total + $detail->montoIceEspecifico + $detail->montoIcePorcentual ,5);
            $new->cantidad = $detail->quantity;
            $new->marcaIce = isset($detail->marcaIce) ? ( in_array($detail->marcaIce,[1,2]) ? $detail->marcaIce : 2 ) : 2;
            $new->alicuotaIva = isset($detail->alicuotaIva) ? $detail->alicuotaIva : 0;
            $new->precioNetoVentaIce = $detail->precioNetoVentaIce;
            $new->alicuotaEspecifica = isset($detail->alicuotaEspecifica) ? $detail->alicuotaEspecifica : 0;
            $new->alicuotaPorcentual = isset($detail->alicuotaPorcentual) ? $detail->alicuotaPorcentual : 0;
            $new->montoIceEspecifico = isset($detail->montoIceEspecifico) ? $detail->montoIceEspecifico : 0;
            $new->montoIcePorcentual = isset($detail->montoIcePorcentual) ? $detail->montoIcePorcentual : 0;
            $new->cantidadIce = isset($detail->cantidadIce) ? $detail->cantidadIce : 0;

            if ($detail->discount > 0)
                $new->montoDescuento = round((float)($detail->cost * $detail->quantity) - $detail->line_total,5);

            $new->unidadMedida = $product_sync->codigo_unidad;

            $details[] = $new;

            $total += $new->subTotal;
        }
        $total = $total - round($this->source_data['fel_data_parsed']['descuentoAdicional'], 2);

        
        $totalsujetoiva = $total - round($this->source_data['fel_data_parsed']['montoIceEspecifico'], 2) - round($this->source_data['fel_data_parsed']['montoIcePorcentual'], 2);
        

        \Log::debug("TOTAL:>>>>>>>>>>>>>> " .json_encode([$totalsujetoiva, $total,round($this->source_data['fel_data_parsed']['montoGiftCard'], 2) , round($this->source_data['fel_data_parsed']['descuentoAdicional'], 2)]));
        return [
            "tipoCambio" => $this->source_data['fel_data_parsed']['tipo_cambio'],
            "montoTotal" => $total,
            "montoTotalMoneda" => round($total / $this->source_data['fel_data_parsed']['tipo_cambio'],2),
            "montoTotalSujetoIva" => $totalsujetoiva ,
            "detalles" => $details
        ];
    }

    public function getFelInvoice(): FelInvoiceRequest
    {
        return $this->fel_invoice;
    }
}
