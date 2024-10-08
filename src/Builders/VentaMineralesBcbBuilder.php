<?php

namespace EmizorIpx\ClientFel\Builders;

use EmizorIpx\ClientFel\Contracts\FelInvoiceBuilderInterface;
use EmizorIpx\ClientFel\Models\FelInvoiceRequest;
use EmizorIpx\ClientFel\Models\FelSyncProduct;
use Exception;
use stdClass;
use Hashids\Hashids;

class VentaMineralesBcbBuilder extends BaseFelInvoiceBuilder implements FelInvoiceBuilderInterface
{

    protected $fel_invoice;

    public function __construct($data)
    {
        parent::__construct($data);
    }

    public function prepare(): FelInvoiceRequest
    {

        if ($this->source_data['update'])
            $this->fel_invoice = $this->getFelInvoiceFirstOrFail();
        else
            $this->fel_invoice = new FelInvoiceRequest();

        return $this->fel_invoice;
    }

    public function processInput(): FelInvoiceRequest
    {
        try {
            $input = array_merge(
                $this->input,
                [
                    "direccionComprador" => $this->source_data['fel_data_parsed']["direccionComprador"],
                    "ruex" => $this->source_data['company']->ruex,
                    "nim" => $this->source_data['company']->nim,
                    "concentradoGranel" => $this->source_data['fel_data_parsed']["concentradoGranel"],
                    "origen" => $this->source_data['fel_data_parsed']["origen"],
                    "numeroLote" => $this->source_data['fel_data_parsed']["numeroLote"],
                    "kilosNetosHumedos" => $this->source_data['fel_data_parsed']["kilosNetosHumedos"],
                    "humedadPorcentaje" => $this->source_data['fel_data_parsed']["humedadPorcentaje"],
                    "humedadValor" => $this->source_data['fel_data_parsed']["humedadValor"],
                    "mermaPorcentaje" => $this->source_data['fel_data_parsed']["mermaPorcentaje"],
                    "mermaValor" => $this->source_data['fel_data_parsed']["mermaValor"],
                    "kilosNetosSecos" => $this->source_data['fel_data_parsed']["kilosNetosSecos"],
                    "gastosRealizacion" => $this->source_data['fel_data_parsed']["gastosRealizacion"],
                    "montoGiftCard" => round($this->source_data['fel_data_parsed']['montoGiftCard'], 2),
                    "descuentoAdicional" => round($this->source_data['fel_data_parsed']['descuentoAdicional'], 2),
                    "cafc" => $this->source_data['fel_data_parsed']['cafc'],
                ],
                $this->getOtrosDatos(),
                $this->getDetailsAndTotals()
            );

            $this->fel_invoice->fill($input);
        } catch (Exception $ex) {
            \Log::info($ex->getMessage());
        }
        return $this->fel_invoice;
    }

    public function getOtrosDatos(): array
    {

        return [
            "otrosDatos" => []
        ];
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
            $new->codigoProducto =  $product_sync->codigo_producto . ""; // this values was added only frontend Be careful
            $new->codigoProductoSin =  $product_sync->codigo_producto_sin . ""; // this values was added only frontend Be careful
            $new->codigoActividadEconomica =  $product_sync->codigo_actividad_economica . "";
            $new->descripcion = $detail->notes;
            $new->precioUnitario = $detail->cost;
            $new->subTotal = round((float)$detail->line_total, 5);

            $new->cantidad = $detail->quantity;
            $new->cantidadExtraccion = $detail->cantidadExtraccion;

            $new->unidadMedidaExtraccion = $detail->unidadMedidaExtraccion;
            $new->unidadMedida = $product_sync->codigo_unidad;

            $new->numeroSerie = null;


            $new->descripcionLeyes = !empty($detail->leyes) ? $detail->leyes . "" : "";
            $new->codigoNandina = $detail->codigoNandina;



            if ($detail->discount > 0)
                $new->montoDescuento = round(($detail->cost * $detail->quantity) - $detail->line_total, 5);


            $details[] = $new;

            $total += $new->subTotal;
        }

        return [
            "tipoCambio" => $this->source_data['fel_data_parsed']['tipo_cambio'],
            "iva" => $this->source_data['fel_data_parsed']['iva'],
            "liquidacion_preliminar" => $this->source_data['fel_data_parsed']['liquidacionPreliminar'],
            "montoTotalSujetoIva" => $this->source_data['fel_data_parsed']['precioConcentradoBs'],
            "montoTotal" => $this->source_data['fel_data_parsed']['precioConcentradoBs'],
            "gastosRealizacion" => $this->source_data['fel_data_parsed']['gastosRealizacion'],
            "montoTotalMoneda" => $this->source_data['fel_data_parsed']['precioConcentrado'],
            "detalles" => $details
        ];
    }
    public function getFelInvoice(): FelInvoiceRequest
    {
        return $this->fel_invoice;
    }

    public function createOrUpdate(): void
    {
        try {

            $this->fel_invoice->save();
        } catch (\Throwable $th) {
            \Log::info($th);
        }
    }
}
