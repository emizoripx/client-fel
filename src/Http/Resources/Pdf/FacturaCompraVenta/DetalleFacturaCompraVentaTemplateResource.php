<?php

namespace EmizorIpx\ClientFel\Http\Resources\Pdf\FacturaCompraVenta;

use EmizorIpx\ClientFel\Http\Resources\Pdf\BaseDetalleTemplateResource;

class DetalleFacturaCompraVentaTemplateResource extends BaseDetalleTemplateResource {

    public function toArray($request)
    {
        $common = parent::toArray($request);

        return array_merge($common, [
            "especialidad" => isset($this->especialidad) ? $this->especialidad : '',
            "especialidadDetalle" => isset($this->especialidadDetalle) ? $this->especialidadDetalle : '',
            "nroQuirofanoSalaOperaciones" => isset($this->nroQuirofanoSalaOperaciones) ? $this->nroQuirofanoSalaOperaciones : '',
            "especialidadMedico" => isset($this->especialidadMedico) ? $this->especialidadMedico : '',
            "nombreApellidoMedico" => isset($this->nombreApellidoMedico) ? $this->nombreApellidoMedico : '',
            "nitDocumentoMedico" => isset($this->nitDocumentoMedico) ? $this->nitDocumentoMedico : '',
            "nroMatriculaMedico" => isset($this->nroMatriculaMedico) ? $this->nroMatriculaMedico : '',
            "nroFacturaMedico" => isset($this->nroFacturaMedico) ? $this->nroFacturaMedico : '',
        ]);
    }

}