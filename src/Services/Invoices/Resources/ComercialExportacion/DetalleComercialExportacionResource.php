<?php

namespace EmizorIpx\ClientFel\Services\Invoices\Resources\ComercialExportacion;

use Illuminate\Http\Resources\Json\JsonResource;

class DetalleComercialExportacionResource extends JsonResource
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

        if( isset($this->resource['leyes']) ){
            $data_array['extras'] = [
                'leyes' => $this->resource['leyes']
            ];
        }

        return array_merge($data_array, [
            "codigoProducto" => $this->resource['codigoProducto'],
            "codigoActividadSin" => $this->resource['codigoActividadEconomica'],
            "descripcion" => $this->resource['descripcion'],
            "codigoProductoSin" => $this->resource['codigoProductoSin'],
            "cantidad" => $this->resource['cantidad'],
            "precioUnitario" => $this->resource['precioUnitario'],
            "subTotal" => $this->resource['subTotal'],
            "unidadMedida" => $this->resource['unidadMedida'],
            "codigoNandina" => $this->resource['codigoNandina'],
            "montoDescuento" => $this->resource['montoDescuento'] ?? 0
        ]);
    }
}
