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
        return [
            "tax_rate1" => !empty($this->tax_rate1) ? (int) $this->tax_rate1 : 0,
            "tax_rate3" => !empty($this->tax_rate3) ? (int) $this->tax_rate3 : 0,
            "tax_rate2" => !empty($this->tax_rate2) ? (int) $this->tax_rate2 : 0,

            "tax_name1" => !empty($this->tax_name1) ? (string) $this->tax_name1 : "",
            "tax_name2" => !empty($this->tax_name2) ? (string) $this->tax_name2 : "",
            "tax_name3" => !empty($this->tax_name3) ? (string) $this->tax_name3 : "",
            "custom_value1" => !empty($this->custom_value1) ? (string) $this->custom_value1 : "",
            "custom_value2" => !empty($this->custom_value2) ? (string) $this->custom_value2 : "",
            "custom_value3" => !empty($this->custom_value3) ? (string) $this->custom_value3 : "",
            "custom_value4" => !empty($this->custom_value4) ? (string) $this->custom_value4 : "",
            "line_total" => !empty($this->line_total) ? (float) $this->line_total : 0,
            "sort_id" => !empty($this->sort_id) ? (string) $this->sort_id :"0",
            "product_id" => !empty($this->product_id) ? (string) $this->product_id : "",
            "leyes" => !empty($this->leyes) ? $this->leyes."": "",
            "notes" => !empty($this->notes) ? (string) $this->notes : "",
            "type_id" => !empty($this->type_id) ? (string) $this->type_id : "",
            "date" => !empty($this->date) ? (string) $this->date : "",
            "is_amount_discount" => !empty($this->is_amount_discount) ? (bool) $this->is_amount_discount : false,
            "quantity" => !empty($this->quantity) ? (float) $this->quantity : 0,
            "discount" => !empty($this->discount) ? (int) $this->discount : 0,
            "createdAt" => !empty($this->createdAt) ? (int) $this->createdAt : 0,
            "product_key" => !empty($this->product_key) ? (string) $this->product_key : "",
            "cost" => !empty($this->cost) ? (float) $this->cost : 0,
            // "codigoNandina" => !empty($this->codigoNandina) ? (string) $this->codigoNandina : "",
            // "cantidadExtraccion" => !empty($this->cantidadExtraccion) ? $this->cantidadExtraccion : 0,
            // "unidadMedidaExtraccion" => !empty($this->unidadMedidaExtraccion) ? $this->unidadMedidaExtraccion : 0,
            "posicionOriginal" => isset($this->posicionOriginal) ? $this->posicionOriginal : "",
            "codigo_producto" => isset($this->codigo_producto) ? $this->codigo_producto : "",
            // "montoConciliado" =>  isset($this->montoConciliado) ? $this->montoConciliado :"0",
            // "montoFinal" =>  isset($this->montoFinal) ? $this->montoFinal : "0",
            // "montoIehd" =>  isset($this->montoIehd) ? $this->montoIehd : "0",
            "subtotalOriginal" =>  isset($this->subtotalOriginal) ? $this->subtotalOriginal : "0",
        ];
    }
}
