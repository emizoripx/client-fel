<?php

namespace EmizorIpx\ClientFel\Http\Resources\Pdf\FacturaAlcanzadaIce;

use EmizorIpx\ClientFel\Http\Resources\Pdf\BaseDetalleTemplateResource;
use EmizorIpx\ClientFel\Utils\NumberUtils;

class DetalleFacturaAlcanzadaIceTemplateResource extends BaseDetalleTemplateResource {

    public function toArray($request)
    {
        $common = parent::toArray($request);

        $common['precioUnitario'] = NumberUtils::number_format_custom( (float) $this->precioUnitario, 5);
        $common['cantidad'] = NumberUtils::number_format_custom( (float) $this->cantidad, 5) ;
        $common['montoDescuento'] = isset($this->montoDescuento) ? NumberUtils::number_format_custom( (float) $this->montoDescuento, 5) : '0.00000';
        $common['subTotal'] = NumberUtils::number_format_custom( (float) $this->subTotal, 5);

        return array_merge($common, [
            'marcaIce' => $this->marcaIce,
            'alicuotaIva' => isset($this->alicuotaIva) ? NumberUtils::number_format_custom( (float) $this->alicuotaIva, 5) : '0.00000' ,
            'precioNetoVentaIce' => isset($this->precioNetoVentaIce) ? NumberUtils::number_format_custom( (float) $this->precioNetoVentaIce, 5) : '0.00000' ,
            'alicuotaEspecifica' => isset($this->alicuotaEspecifica) ? NumberUtils::number_format_custom( (float) $this->alicuotaEspecifica, 5) : '0.00000' ,
            'alicuotaPorcentual' => isset($this->alicuotaPorcentual) ? NumberUtils::number_format_custom( (float) $this->alicuotaPorcentual, 5) : '0.00000' ,
            'montoIceEspecifico' => isset($this->montoIceEspecifico) ? NumberUtils::number_format_custom( (float) $this->montoIceEspecifico, 5) : '0.00000' ,
            'montoIcePorcentual' => isset($this->montoIcePorcentual) ? NumberUtils::number_format_custom( (float) $this->montoIcePorcentual, 5) : '0.00000' ,
            'cantidadIce' => isset($this->cantidadIce) ? NumberUtils::number_format_custom( (float) $this->cantidadIce, 5) : '0.00000'
        ]);
        
    }

}