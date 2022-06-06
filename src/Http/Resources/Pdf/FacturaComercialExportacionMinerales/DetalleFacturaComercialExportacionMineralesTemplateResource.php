<?php

namespace EmizorIpx\ClientFel\Http\Resources\Pdf\FacturaComercialExportacionMinerales;

use EmizorIpx\ClientFel\Http\Resources\Pdf\BaseDetalleTemplateResource;
use EmizorIpx\ClientFel\Models\Parametric\Unit;
use EmizorIpx\ClientFel\Utils\NumberUtils;

class DetalleFacturaComercialExportacionMineralesTemplateResource extends BaseDetalleTemplateResource {

    public function toArray($request)
    {
        
        $common = parent::toArray($request);

        $common['cantidad'] = NumberUtils::number_format_custom($this->cantidad, 5);
        $common['precioUnitario'] =intval($this->precioUnitario) < $this->precioUnitario ? NumberUtils::number_format_custom( (float) $this->precioUnitario, 5   ) : NumberUtils::number_format_custom( (float) $this->precioUnitario, 5);
        $common["subTotal"] = NumberUtils::number_format_custom( (float) $this->subTotal, 5);

        return array_merge($common, [
            "descripcionLeyes" => $this->descripcionLeyes,
            "codigoNandina" => $this->codigoNandina,
            "cantidadExtraccion" => NumberUtils::number_format_custom((float) bcdiv($this->cantidadExtraccion,'1',5) ,5) ,
            "unidadMedidaExtraccion" => ucwords(strtolower( Unit::getUnitDescription($this->unidadMedidaExtraccion))),
        ]);

    }

}