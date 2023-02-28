<?php

namespace EmizorIpx\ClientFel\Http\Resources\Orders;

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
            "line_total" => !empty($this->line_total) ? (float) $this->line_total : 0,
            "product_id" => !empty($this->product_id) ? (string) $this->product_id : "",
            "notes" => !empty($this->notes) ? (string) $this->notes : "",
            "is_amount_discount" => !empty($this->is_amount_discount) ? (bool) $this->is_amount_discount : false,
            "quantity" => !empty($this->quantity) ? (float) $this->quantity : 0,
            "discount" => !empty($this->discount) ? (int) $this->discount : 0,
            "product_key" => !empty($this->product_key) ? (string) $this->product_key : "",
            "price" => !empty($this->cost) ? (float) $this->cost : 0,
            "nandina" => !empty($this->codigoNandina) ? (string) $this->codigoNandina : "",
            "product_code" => isset($this->codigo_producto) ? $this->codigo_producto : "",
        ];
    }
}
