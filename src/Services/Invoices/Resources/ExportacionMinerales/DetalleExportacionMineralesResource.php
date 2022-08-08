<?php

namespace EmizorIpx\ClientFel\Services\Invoices\Resources\ExportacionMinerales;

use Illuminate\Http\Resources\Json\JsonResource;

class DetalleExportacionMineralesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $data_array = [];

        if( isset($this->resource['valorBruto']) ){
            $data_array = array_merge($data_array, [
                'valorBruto' => $this->resource['valorBruto']
            ]);
        }
        if( isset($this->resource['alicuota']) ){
            $data_array = array_merge($data_array, [
                'alicuota' => $this->resource['alicuota']
            ]);
        }

        if( isset($this->resource['lfotfRegalia']) ){
            $data_array = array_merge( $data_array, [
                'lfotfRegalia' => $this->resource['lfotfRegalia']
            ]);
        }
        if( isset($this->resource['cotizacionRegalia']) ){
            $data_array = array_merge($data_array, [
                'cotizacionRegalia' => $this->resource['cotizacionRegalia']
            ]);
        }

        $extras = count($data_array) > 1 ? [ 'extras' => $data_array ] : [];
        return array_merge( $extras  , [
            "descripcionLeyes" => $this->resource['descripcionLeyes'],
            "codigoProductoSin" => $this->resource['codigoProductoSin'],
            "codigoNandina" => $this->resource['codigoNandina'],
            "codigoProducto" => $this->resource['codigoProducto'],
            "codigoActividadSin" => $this->resource['codigoActividadEconomica'],
            "descripcion" => $this->resource['descripcion'],
            "cantidad" => $this->resource['cantidad'],
            "cantidadExtraccion" => $this->resource['cantidadExtraccion'],
            "precioUnitario" => $this->resource['precioUnitario'],
            "subTotal" => $this->resource['subTotal'],
            "unidadMedida" => $this->resource['unidadMedida'],
            "unidadMedidaExtraccion" => $this->resource['unidadMedidaExtraccion'],
            "montoDescuento" => $this->resource['montoDescuento'] ?? 0
        ]);
    }
}
