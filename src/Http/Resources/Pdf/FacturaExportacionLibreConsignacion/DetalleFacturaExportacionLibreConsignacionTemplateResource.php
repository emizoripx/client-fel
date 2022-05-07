<?php

namespace EmizorIpx\ClientFel\Http\Resources\Pdf\FacturaExportacionLibreConsignacion;

use EmizorIpx\ClientFel\Http\Resources\Pdf\BaseDetalleTemplateResource;
use EmizorIpx\ClientFel\Utils\NumberUtils;

class DetalleFacturaExportacionLibreConsignacionTemplateResource extends BaseDetalleTemplateResource {

    public function toArray($request)
    {
        
        $common = parent::toArray($request);

        $common['cantidad'] = intval($this->cantidad) < $this->cantidad ? NumberUtils::number_format_custom( (float) $this->cantidad, 5) : NumberUtils::number_format_custom( (float) $this->cantidad, 5);
        $common['precioUnitario'] = intval($this->precioUnitario) < $this->precioUnitario ? NumberUtils::number_format_custom( (float) $this->precioUnitario, 5) : NumberUtils::number_format_custom( (float) $this->precioUnitario, 5);
        $common['montoDescuento'] = isset($this->montoDescuento) ? NumberUtils::number_format_custom( (float) $this->montoDescuento, 5) : '0.00000' ;
        $common['subTotal'] = NumberUtils::number_format_custom( (float) bcdiv( $this->subTotal ,'1',5), 5);

        return array_merge( $common, [
            "codigoNandina" => $this->codigoNandina
        ]);

    }

}