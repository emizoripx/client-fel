<?php

namespace EmizorIpx\ClientFel\Http\Resources\Pdf\FacturaEngarrafadoras;

use EmizorIpx\ClientFel\Http\Resources\Pdf\BaseDetalleTemplateResource;
use EmizorIpx\ClientFel\Utils\NumberUtils;

class DetalleFacturaEngarrafadorasTemplateResource extends BaseDetalleTemplateResource {

    public function toArray($request)
    {
        $common = parent::toArray($request);

        $common['precioUnitario'] = NumberUtils::number_format_custom( (float) $this->precioUnitario, 5);
        $common['cantidad'] = NumberUtils::number_format_custom( (float) $this->cantidad, 5) ;
        $common['montoDescuento'] = isset($this->montoDescuento) ? NumberUtils::number_format_custom( (float) $this->montoDescuento, 5) : '0.00000';
        $common['subTotal'] = NumberUtils::number_format_custom( (float) $this->subTotal, 5);

        return $common;
        
    }

}