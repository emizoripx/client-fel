<?php

namespace EmizorIpx\ClientFel\Services\Invoices\Resources\Clinicas;

use Illuminate\Http\Resources\Json\JsonResource;

class DetalleClinicasResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "codigoProducto" => $this->resource['codigoProducto'],
            "descripcion" => $this->resource['descripcion'],
            "codigoProductoSin" => $this->resource['codigoProductoSin'],
            "codigoActividadSin" => $this->resource['codigoActividadEconomica'],
            "cantidad" => $this->resource['cantidad'],
            "precioUnitario" => $this->resource['precioUnitario'],
            "subTotal" => round($this->resource['subTotal'], 2),
            "montoDescuento" => !empty($this->resource['montoDescuento']) ? round($this->resource['montoDescuento'], 2) : null,
            "unidadMedida" => $this->resource['unidadMedida'],
            
            "especialidad" => $this->resource['especialidad'],
            "especialidadDetalle" => $this->resource['especialidadDetalle'],
            "nroQuirofanoSalaOperaciones" => $this->resource['nroQuirofanoSalaOperaciones'],
            "especialidadMedico" => $this->resource['especialidadMedico'],
            "nombreApellidoMedico" => $this->resource['nombreApellidoMedico'],
            "nroMatriculaMedico" => $this->resource['nroMatriculaMedico'],
            "nitDocumentoMedico" => $this->resource['nitDocumentoMedico'],
            "nroFacturaMedico" => $this->resource['nroFacturaMedico'],
        ];
    }
}
