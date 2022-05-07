<?php

namespace EmizorIpx\ClientFel\Http\Resources\Pdf\FacturaComercialExportacion;

use EmizorIpx\ClientFel\Http\Resources\Pdf\BaseDetalleTemplateResource;
use EmizorIpx\ClientFel\Utils\NumberUtils;

class DetalleFacturaComercialExportacionTemplateResource extends BaseDetalleTemplateResource {

    public function toArray($request)
    {
        
        $common = parent::toArray($request);

        $common['cantidad'] = NumberUtils::number_format_custom($this->cantidad, 5);
        $common['precioUnitario'] =intval($this->precioUnitario) < $this->precioUnitario ? NumberUtils::number_format_custom( (float) $this->precioUnitario, 5   ) : NumberUtils::number_format_custom( (float) $this->precioUnitario, 5);
        $common["montoDescuento"] = isset($this->montoDescuento) ?  NumberUtils::number_format_custom( (float) $this->montoDescuento, 5) : '0.0000';
        $common["subTotal"] = NumberUtils::number_format_custom( (float) $this->subTotal, 5);

        return array_merge($common, [
            "codigoNandina" => $this->codigoNandina
        ]);

    }

}