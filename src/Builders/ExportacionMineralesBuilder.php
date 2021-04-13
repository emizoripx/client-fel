<?php

namespace EmizorIpx\ClientFel\Builders;

use EmizorIpx\ClientFel\Contracts\FelInvoiceBuilderInterface;
use EmizorIpx\ClientFel\Models\FelInvoiceRequest;
use EmizorIpx\ClientFel\Models\FelSyncProduct;
use stdClass;
use Hashids\Hashids;
class ExportacionMineralesBuilder extends BaseFelInvoiceBuilder implements FelInvoiceBuilderInterface
{

    protected $fel_invoice;

    public function __construct($data)
    {
        parent::__construct($data);
    }

    public function prepare(): FelInvoiceRequest
    {
        if ($this->source_data['update'])
            $this->fel_invoice = FelInvoiceRequest::whereIdOrigin($this->source_data['model']->id)->whereNull('cuf')->firstOrFail();
        else
            $this->fel_invoice = new FelInvoiceRequest();

        return $this->fel_invoice;
    }

    public function processInput(): FelInvoiceRequest
    {
        $input = array_merge(
            $this->input,
            [
                "direccionComprador" => $this->source_data["direccionComprador"],
                "ruex" => $this->source_data["ruex"],
                "nim" => $this->source_data["nim"],
                "concentradoGranel" => $this->source_data["concentradoGranel"],
                "origen" => $this->source_data["origen"],
                "puertoTransito" => $this->source_data["puertoTransito"],
                "puertoDestino" => $this->source_data["puertoDestino"],
                "paisDestino" => $this->source_data["paisDestino"],
                "incoterm" => $this->source_data["incoterm"],
                "tipoCambioANB" => $this->source_data["tipoCambioANB"],
                "numeroLote" => $this->source_data["numeroLote"],
                "kilosNetosHumedos" => $this->source_data["kilosNetosHumedos"],
                "humedadPorcentaje" => $this->source_data["humedadPorcentaje"],
                "humedadValor" => $this->source_data["humedadValor"],
                "mermaPorcentaje" => $this->source_data["mermaPorcentaje"],
                "mermaValor" => $this->source_data["mermaValor"],
                "kilosNetosSecos" => $this->source_data["kilosNetosSecos"],
                "gastosRealizacion" => $this->source_data["gastosRealizacion"]
            ],
            $this->getOtrosDatos(),
            $this->getDetailsAndTotals()
        );

        $this->fel_invoice->fill($input);

        return $this->fel_invoice;
    }

    public function getOtrosDatos(): array
    {
        return [
            "otrosDatos" => json_encode([
                "valorFobFrontera" => $this->source_data['fel_data_parsed']['valorFobFrontera'],
                "fleteInternoUSD" => $this->source_data['fel_data_parsed']['fleteInterno'],
                "valorPlata" => $this->source_data['fel_data_parsed']['valorPlata'],
                "valorFobFronteraBs" => $this->source_data['fel_data_parsed']['valorFobFronteraBs'],
                "monedaTransaccional" => $this->source_data['fel_data_parsed']['monedaTransaccional'],
                "partidaArancelaria" => $this->source_data['fel_data_parsed']['partidaArancelaria']
            ])
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
            $new->descripcion = $detail->notes;
            $new->precioUnitario = $detail->cost;
            $new->subTotal = $detail->line_total;
            $new->cantidad = $detail->quantity;
            $new->numeroSerie = null;

            $new->descripcionLeyes = $detail->descripcionLeyes;
            $new->cantidadExtraccion = $detail->quantity;
            $new->unidadMedidaExtraccion = $detail->unidadMedidaExtraccion;
            $new->codigoNandina = $new->codigoNandina;

            if ($detail->discount > 0)
                $new->montoDescuento = ($detail->cost * $detail->quantity) - $detail->line_total;

            $new->numeroImei = null;

            $new->unidadMedida = $detail->unidadMedidaExtraccion;

            $details[] = $new;

            $total += $new->subTotal;
        }

        return [
            "tipoCambio" => $this->source_data['fel_data_parsed']['tipo_cambio'],
            "montoTotal" => $total,
            "gastosRealizacion" => $this->source_data['fel_data_parsed']['gastosRealizacion'],
            "montoTotalMoneda" => $this->source_data['fel_data_parsed']['valorFobFronteraBs'],
            "montoTotalSujetoIva" => 0,
            "detalles" => json_encode($details)
        ];
    }
    public function getFelInvoice(): FelInvoiceRequest
    {
        return $this->fel_invoice;
    }

    public function createOrUpdate(): void
    {
        $this->fel_invoice->save();
    }
    
}
