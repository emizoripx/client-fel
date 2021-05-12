<?php

namespace EmizorIpx\ClientFel\Builders;

use EmizorIpx\ClientFel\Contracts\FelInvoiceBuilderInterface;
use EmizorIpx\ClientFel\Models\FelInvoiceRequest;
use EmizorIpx\ClientFel\Models\FelSyncProduct;
use Hashids\Hashids;
use stdClass;

class CreditoDebitoBuilder extends BaseFelInvoiceBuilder implements FelInvoiceBuilderInterface
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
        // find origin invoice
        $invoice_origin = FelInvoiceRequest::whereCuf( $this->source_data['fel_data_parsed']["numeroAutorizacionCuf"] )->first();

        $input = array_merge(
            $this->input,
            [
                "factura_original_id" => $invoice_origin->id,
                "numeroFacturaOriginal" => $invoice_origin->numeroFactura,
                "numeroAutorizacionCuf" => $this->source_data['fel_data_parsed']["numeroAutorizacionCuf"],
                "montoDescuentoCreditoDebito" => $this->source_data['fel_data_parsed']["montoDescuentoCreditoDebito"],
                "montoEfectivoCreditoDebito" => $this->source_data['fel_data_parsed']["montoEfectivoCreditoDebito"],
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

            $new = new stdClass;
            $new->codigoProducto =  $detail->product_key . ""; // this values was added only frontend Be careful
            $new->codigoProductoSin =  $product_sync->codigo_producto_sin . ""; // this values was added only frontend Be careful
            $new->descripcion = $detail->notes;
            $new->precioUnitario = $detail->cost;
            $new->subTotal = round((float)$detail->line_total,5);
            $new->cantidad = $detail->quantity;
            $new->numeroSerie = null;

            if ($detail->discount > 0)
                $new->montoDescuento = round((float)($detail->cost * $detail->quantity) - $detail->line_total,5);

            $new->unidadMedida = $product_sync->codigo_unidad;

            $details[] = $new;

            $total += $new->subTotal;
        }

        return [
            "tipoCambio" => $this->source_data['fel_data_parsed']['tipo_cambio'],
            "montoTotal" => $this-> source_data['fel_data_parsed']['montoTotal'],
            "montoTotalMoneda" => round($total / $this->source_data['fel_data_parsed']['tipo_cambio'],2),
            "montoTotalSujetoIva" => $this-> source_data['fel_data_parsed']['montoTotal'],
            "detalles" => $details
        ];
    }

    public function getFelInvoice(): FelInvoiceRequest
    {
        return $this->fel_invoice;
    }
}
