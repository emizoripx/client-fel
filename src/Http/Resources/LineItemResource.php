<?php

namespace EmizorIpx\ClientFel\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LineItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $item = [
            "quantity" => !empty($this->quantity) ? (float) $this->quantity : 0,
            "cost" => !empty($this->cost) ? (float) $this->cost : 0,
            "product_key" => !empty($this->product_key) ? (string) $this->product_key : "",
            "notes" => !empty($this->notes) ? (string) $this->notes : "",
            "discount" => !empty($this->discount) ? (float) $this->discount : 0,
            "is_amount_discount" => !empty($this->is_amount_discount) ? (bool) $this->is_amount_discount : false,
            "type_id" => !empty($this->type_id) ? (string) $this->type_id : "1",
            "custom_value1" => !empty($this->custom_value1) ? (string) $this->custom_value1 : "",
            "custom_value2" => !empty($this->custom_value2) ? (string) $this->custom_value2 : "",
            "custom_value3" => !empty($this->custom_value3) ? (string) $this->custom_value3 : "",
            "custom_value4" => !empty($this->custom_value4) ? (string) $this->custom_value4 : "",
            "line_total" => !empty($this->line_total) ? (float) $this->line_total : 0,
            "sort_id" => !empty($this->sort_id) ? (string) $this->sort_id : "0",
            "product_id" => !empty($this->product_id) ? (string) $this->product_id : "",
            "codigo_producto" => isset($this->codigo_producto) ? (string) $this->codigo_producto : "",
            "codigoNandina" => !empty($this->codigoNandina) ? (string) $this->codigoNandina : "",
            "createdAt" => !empty($this->createdAt) ? (int) $this->createdAt : 0,
        ];

        // Taxes (solo si existen)
        if (isset($this->tax_rate1) && $this->tax_rate1 !== '') {
            $item["tax_rate1"] = (float) ($this->tax_rate1 ?? 0);
            $item["tax_name1"] = (string) ($this->tax_name1 ?? "");
        }
        if (isset($this->tax_rate2) && $this->tax_rate2 !== '') {
            $item["tax_rate2"] = (float) ($this->tax_rate2 ?? 0);
            $item["tax_name2"] = (string) ($this->tax_name2 ?? "");
        }
        if (isset($this->tax_rate3) && $this->tax_rate3 !== '') {
            $item["tax_rate3"] = (float) ($this->tax_rate3 ?? 0);
            $item["tax_name3"] = (string) ($this->tax_name3 ?? "");
        }

        // Minería
        if (isset($this->leyes) && $this->leyes !== '') $item["leyes"] = (string) $this->leyes;
        if (isset($this->cantidadExtraccion) && $this->cantidadExtraccion !== '') $item["cantidadExtraccion"] = (float) $this->cantidadExtraccion;
        if (isset($this->unidadMedidaExtraccion) && $this->unidadMedidaExtraccion !== '') $item["unidadMedidaExtraccion"] = (int) $this->unidadMedidaExtraccion;

        // Conciliación
        if (isset($this->isConciliacion) && $this->isConciliacion !== '') $item["isConciliacion"] = (bool) $this->isConciliacion;
        if (isset($this->montoConciliado) && $this->montoConciliado !== '') $item["montoConciliado"] = (string) $this->montoConciliado;
        if (isset($this->montoFinal) && $this->montoFinal !== '') $item["montoFinal"] = (string) $this->montoFinal;
        if (isset($this->subtotalOriginal) && $this->subtotalOriginal !== '') $item["subtotalOriginal"] = (string) $this->subtotalOriginal;

        // IEHD / UFV / Rangos
        if (isset($this->montoIehd) && $this->montoIehd !== '') $item["montoIehd"] = (string) $this->montoIehd;
        if (isset($this->montoUFV) && $this->montoUFV !== '') $item["montoUFV"] = (float) $this->montoUFV;
        if (isset($this->rango) && $this->rango !== '') $item["rango"] = (string) $this->rango;

        // Notas Entrega / Recepción
        if (isset($this->cantidadEntrega) && $this->cantidadEntrega !== '') $item["cantidadEntrega"] = (float) $this->cantidadEntrega;
        if (isset($this->cantidadDevuelto) && $this->cantidadDevuelto !== '') $item["cantidadDevuelto"] = (float) $this->cantidadDevuelto;
        if (isset($this->posicionOriginal) && $this->posicionOriginal !== '') $item["posicionOriginal"] = (string) $this->posicionOriginal;

        // ICE
        if (isset($this->marcaIce) && $this->marcaIce !== '') $item["marcaIce"] = (int) $this->marcaIce;
        if (isset($this->alicuotaIva) && $this->alicuotaIva !== '') $item["alicuotaIva"] = (float) $this->alicuotaIva;
        if (isset($this->precioNetoVentaIce) && $this->precioNetoVentaIce !== '') $item["precioNetoVentaIce"] = (float) $this->precioNetoVentaIce;
        if (isset($this->alicuotaEspecifica) && $this->alicuotaEspecifica !== '') $item["alicuotaEspecifica"] = (float) $this->alicuotaEspecifica;
        if (isset($this->alicuotaPorcentual) && $this->alicuotaPorcentual !== '') $item["alicuotaPorcentual"] = (float) $this->alicuotaPorcentual;
        if (isset($this->montoIceEspecifico) && $this->montoIceEspecifico !== '') $item["montoIceEspecifico"] = (float) $this->montoIceEspecifico;
        if (isset($this->montoIcePorcentual) && $this->montoIcePorcentual !== '') $item["montoIcePorcentual"] = (float) $this->montoIcePorcentual;
        if (isset($this->cantidadIce) && $this->cantidadIce !== '') $item["cantidadIce"] = (float) $this->cantidadIce;

        // Hoteles / Recibos
        if (isset($this->detalleHuespedes) && $this->detalleHuespedes !== '') $item["detalleHuespedes"] = (string) $this->detalleHuespedes;
        if (isset($this->is_receipt) && $this->is_receipt !== '') $item["is_receipt"] = (bool) $this->is_receipt;

        
        // Hospitales / Clínicas / Médicos
        $nitDoc = data_get($this->resource, 'nitDocumentoMedico', $this->nitDocumentoMedico ?? null);
        if (!is_null($nitDoc) && $nitDoc !== '') $item["nitDocumentoMedico"] = (string) $nitDoc;

        $nomMed = data_get($this->resource, 'nombreApellidoMedico', $this->nombreApellidoMedico ?? null);
        if (!is_null($nomMed) && $nomMed !== '') $item["nombreApellidoMedico"] = (string) $nomMed;

        $matMed = data_get($this->resource, 'nroMatriculaMedico', $this->nroMatriculaMedico ?? null);
        if (!is_null($matMed) && $matMed !== '') $item["nroMatriculaMedico"] = (string) $matMed;

        $facMed = data_get($this->resource, 'nroFacturaMedico', $this->nroFacturaMedico ?? null);
        if (!is_null($facMed) && $facMed !== '') $item["nroFacturaMedico"] = (string) $facMed;

        $quiMed = data_get($this->resource, 'nroQuirofanoSalaOperaciones', $this->nroQuirofanoSalaOperaciones ?? null);
        if (!is_null($quiMed) && $quiMed !== '') $item["nroQuirofanoSalaOperaciones"] = (string) $quiMed;

        $espMed = data_get($this->resource, 'especialidadMedico', $this->especialidadMedico ?? null);
        if (!is_null($espMed) && $espMed !== '') $item["especialidadMedico"] = (string) $espMed;

        $esp = data_get($this->resource, 'especialidad', $this->especialidad ?? null);
        if (!is_null($esp) && $esp !== '') $item["especialidad"] = (string) $esp;

        $espDet = data_get($this->resource, 'especialidadDetalle', $this->especialidadDetalle ?? null);
        if (!is_null($espDet) && $espDet !== '') $item["especialidadDetalle"] = (string) $espDet;

        return $item;
    }
}
