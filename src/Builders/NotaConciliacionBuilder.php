<?php

namespace EmizorIpx\ClientFel\Builders;

use EmizorIpx\ClientFel\Contracts\FelInvoiceBuilderInterface;
use EmizorIpx\ClientFel\Models\FelInvoiceRequest;
use EmizorIpx\ClientFel\Models\FelSyncProduct;
use Hashids\Hashids;
use stdClass;

class NotaConciliacionBuilder extends BaseFelInvoiceBuilder implements FelInvoiceBuilderInterface
{
    protected $fel_invoice;

    public function __construct($data)
    {
        parent::__construct($data);
    }

    public function processInput(): void
    {
        // find origin invoice
        $invoice_origin = FelInvoiceRequest::whereCuf( $this->source_data['fel_data_parsed']["numeroAutorizacionCuf"] )->first();

        $input = array_merge(
            $this->input,
            [
                "factura_original_id" => !is_null($invoice_origin) ? $invoice_origin->id_origin : null,
                "facturaExterna" => !is_null($invoice_origin) ? 0 : 1,
                "numeroAutorizacionCuf" => $this->source_data['fel_data_parsed']["numeroAutorizacionCuf"],
                "external_invoice_data" => [
                        "numeroFacturaOriginal" => $this->source_data['fel_data_parsed']["numeroFacturaOriginal"],
                        "codigoControl" => $this->source_data['fel_data_parsed']["codigoControl"],
                        "fechaEmisionOriginal" => $this->source_data['fel_data_parsed']["fechaEmisionOriginal"],
                        "montoTotalOriginal" => $this->source_data['fel_data_parsed']["montoTotalOriginal"],
                    ],
                "creditoFiscalIva" => $this->source_data['fel_data_parsed']["creditoFiscalIva"],
                "debitoFiscalIva" => $this->source_data['fel_data_parsed']["debitoFiscalIva"],
            ],
            $this->getDetailsAndTotals()
        );

    }

    public function getDetailsAndTotals(): array
    {
        $line_items = $this->source_data['model']->line_items;
        $details = [];
        $details_nc = [];
        $model = $this->source_data['model'];

        $total = 0;

        $hashid = new Hashids(config('ninja.hash_salt'), 10);

        foreach ($line_items as $detail) {

            $id_origin = $hashid->decode($detail->product_id)[0];

            $product_sync = FelSyncProduct::whereIdOrigin($id_origin)->whereCompanyId($model->company_id)->withTrashed()->first();

            $new = new \stdClass;
            $new->codigoProducto =  $product_sync->codigo_producto. ""; // this values was added only frontend Be careful
            $new->codigoProductoSin =  $product_sync->codigo_producto_sin . ""; // this values was added only frontend Be careful
            $new->codigoActividadEconomica =  $product_sync->codigo_actividad_economica . "";
            $new->descripcion = $detail->notes;
            $new->precioUnitario = $detail->cost;
            $new->subTotal = round((float)$detail->line_total,5);
            $new->cantidad = $detail->quantity;
            $new->unidadMedida = $product_sync->codigo_unidad;

            if (!$detail->isConciliacion) {
    
                if ($detail->discount > 0)
                    $new->montoDescuento = round((float)($detail->cost * $detail->quantity) - $detail->line_total,5);
                $details[] = $new;
            } else {
                $new->montoOriginal = $detail->montoOriginal;
                $new->montoFinal = $detail->montoFinal;
                $new->montoConciliado = $detail->montoConciliado;
                $details_nc[] = $new;
            }
            
            
        }

        return [
            "tipoCambio" => 1,
            "montoTotal" => $this->source_data['fel_data_parsed']['montoTotalConciliado'],
            "montoTotalMoneda" => 0,
            "montoTotalSujetoIva" => 0,
            "detalles" => ["original" => $details, "conciliado" => $details_nc],
        ];
    }

    public function getFelInvoice(): FelInvoiceRequest
    {
        return $this->fel_invoice;
    }
}
