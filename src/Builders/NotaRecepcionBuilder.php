<?php

namespace EmizorIpx\ClientFel\Builders;

use EmizorIpx\ClientFel\Contracts\FelInvoiceBuilderInterface;
use EmizorIpx\ClientFel\Models\FelInvoiceRequest;
use EmizorIpx\ClientFel\Models\FelSyncProduct;
use Hashids\Hashids;
use Carbon\Carbon;
use stdClass;

class NotaRecepcionBuilder extends BaseFelInvoiceBuilder implements FelInvoiceBuilderInterface
{
    protected $fel_invoice;

    public function __construct($data)
    {
        parent::__construct($data);
    }

    public function prepare(): FelInvoiceRequest
    {
        if ($this->source_data['update'])
            $this->fel_invoice = FelInvoiceRequest::whereIdOrigin($this->source_data['model']->id)->firstOrFail();
        else
            $this->fel_invoice = new FelInvoiceRequest();

        return $this->fel_invoice;
    }

    public function processInput(): FelInvoiceRequest
    {
        // find origin invoice
        $hashid = new Hashids(config('ninja.hash_salt'), 10);

        $this->input['fechaEmision'] = Carbon::parse($this->input['fechaEmision'])->toDateTimeString();

        $input = array_merge(
            $this->input,
            [
                "codigoMetodoPago" => 1,
                "numeroTarjeta" => null,
                "codigoLeyenda" => 1,
                "codigoActividad" => 1,
                "codigoPuntoVenta" => 0,
                "codigoMoneda" => 1,
                "type_invoice_id" => 1
            ],
            $this->getDetailsAndTotals()
        );
        
        $this->fel_invoice->fill($input);
        
        return $this->fel_invoice;
    }

    public function createOrUpdate():void
    {
        try {

            $this->fel_invoice->save();
    
        } catch( \Exception $ex ) {

            \Log::debug("Error al guardar nota de recepción: " . $ex->getMessage() . " File: " . $ex->getFile() . " Line: " . $ex->getLine());

        }

    }

    public function getDetailsAndTotals(): array
    {
        $line_items = $this->source_data['model']->line_items;
        $details = [];
        $model = $this->source_data['model'];

        $total = 0;

        $hashid = new Hashids(config('ninja.hash_salt'), 10);

        foreach ($line_items as $detail) {

            $id_origin = $hashid->decode($detail->product_id)[0];

            $product_sync = FelSyncProduct::whereIdOrigin($id_origin)->whereCompanyId($model->company_id)->withTrashed()->first();

            $new = new stdClass;
            $new->codigoProducto =  $product_sync->codigo_producto. ""; // this values was added only frontend Be careful
            $new->codigoProductoSin =  $product_sync->codigo_producto_sin . ""; // this values was added only frontend Be careful
            $new->codigoActividadEconomica =  $product_sync->codigo_actividad_economica . "";
            $new->descripcion = $detail->notes;
            $new->precioUnitario = $detail->cost;
            $new->subTotal = round((float)$detail->line_total,5);
            $new->cantidad = $detail->quantity;
            $new->cantidadVendido = $detail->quantity;
            $new->rango = $detail->rango;

            $new->unidadMedida = $product_sync->codigo_unidad;

            $details[] = $new;

            $total += $new->subTotal;
            
        }

        return [
            "tipoCambio" => 1,
            "montoTotal" => $total,
            "montoTotalMoneda" => $total,
            "montoTotalSujetoIva" => 0,
            "detalles" => $details,
        ];
    }

    public function getFelInvoice(): FelInvoiceRequest
    {
        return $this->fel_invoice;
    }
}
