<?php

namespace EmizorIpx\ClientFel\Builders;

use EmizorIpx\ClientFel\Contracts\FelInvoiceBuilderInterface;
use EmizorIpx\ClientFel\Models\FelInvoiceRequest;
use EmizorIpx\ClientFel\Models\FelSyncProduct;
use Hashids\Hashids;
use stdClass;

class ComercializacionHidrocarburosBuilder extends BaseFelInvoiceBuilder implements FelInvoiceBuilderInterface
{
    protected $fel_invoice;

    public function __construct($data)
    {
        parent::__construct($data);
    }

    public function processInput(): void
    {
        $this->input = array_merge(
            $this->input,[
                "montoTotalSujetoIva" => $this->source_data['fel_data_parsed']["montoTotalSujetoIva"],
                "descuentoAdicional" => round($this->source_data['fel_data_parsed']['descuentoAdicional'],2),
                "cafc" => $this->source_data['fel_data_parsed']['cafc'],
                "data_specific_by_sector" => [
                    "paisDestino" => $this->source_data['fel_data_parsed']["paisDestino"],
                    "tipoEnvase" => $this->source_data['fel_data_parsed']["tipoEnvase"],
                    "placaVehiculo" => $this->source_data['fel_data_parsed']["placaVehiculo"],
                    "codigoAutorizacionSC" => $this->source_data['fel_data_parsed']["codigoAutorizacionSC"],
                    "observacion" => $this->source_data['fel_data_parsed']["observacion"],
                ],
            ],
            $this->getDetailsAndTotals()
        );

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

            $new = new stdClass;
            $new->codigoProducto =  $product_sync->codigo_producto  . ""; // this values was added only frontend Be careful
            $new->codigoProductoSin =  $product_sync->codigo_producto_sin . ""; // this values was added only frontend Be careful
            $new->codigoActividadEconomica =  $product_sync->codigo_actividad_economica . "";
            $new->descripcion = $detail->notes;
            $new->precioUnitario = $detail->cost;
            $new->subTotal = round((float)$detail->line_total,5);
            $new->cantidad = $detail->quantity;

            if ($detail->discount > 0)
                $new->montoDescuento = round((float)($detail->cost * $detail->quantity) - $detail->line_total,5);

            $new->unidadMedida = $product_sync->codigo_unidad;

            $details[] = $new;

            $total += $new->subTotal;
        }
        $total = $total - round($this->source_data['fel_data_parsed']['descuentoAdicional'], 2);

        \Log::debug("gift card  >>>" . round($this->source_data['fel_data_parsed']['montoGiftCard'], 2) );

        $montoTotalSujetoIva = $total * 0.70;
        

        \Log::debug("TOTAL:>>>>>>>>>>>>>> " .json_encode([$montoTotalSujetoIva, $total,round($this->source_data['fel_data_parsed']['montoGiftCard'], 2) , round($this->source_data['fel_data_parsed']['descuentoAdicional'], 2)]));
        return [
            "tipoCambio" => $this->source_data['fel_data_parsed']['tipo_cambio'],
            "montoTotal" => $total,
            "montoTotalMoneda" => round($total / $this->source_data['fel_data_parsed']['tipo_cambio'],2),
            "montoTotalSujetoIva" => $montoTotalSujetoIva ,
            "detalles" => $details
        ];
    }

    public function getFelInvoice(): FelInvoiceRequest
    {
        return $this->fel_invoice;
    }
}
