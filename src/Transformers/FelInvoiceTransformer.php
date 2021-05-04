<?php

namespace EmizorIpx\ClientFel\Transformers;

use App\Transformers\EntityTransformer;
use App\Utils\Traits\MakesHash;
use EmizorIpx\ClientFel\Models\FelInvoiceRequest;

class FelInvoiceTransformer extends EntityTransformer{


    use MakesHash;

    protected $serializer;

    protected $defaultIncludes = [];

    protected $availableIncludes = [];

    public function __construct($serializer = null)
    {
        $this->serializer = $serializer;
    }


    public function transform( FelInvoiceRequest $felInvoice ){

        \Log::debug("FelInvoice");
        \Log::debug($felInvoice);

        return [
            "id" => (int) $felInvoice->id,
            "company_id" => $felInvoice->company_id,
            "id_origin" => $felInvoice->id_origin,
            "codigoMetodoPago" =>(int) $felInvoice->codigoMetodoPago,
            "codigoLeyenda" => (int) $felInvoice->codigoLeyenda,
            "codigoActividad" => (int) $felInvoice->codigoActividad,
            "numeroFactura" => (int) $felInvoice->numeroFactura,
            "fechaEmision" => $felInvoice->fechaEmision,
            "nombreRazonSocial" => $felInvoice->nombreRazonSocial,
            "codigoTipoDocumentoIdentidad" => (int) $felInvoice->codigoTipoDocumentoIdentidad,
            "numeroDocumento" => $felInvoice->numeroDocumento,
            "complemento" => $felInvoice->complemento,
            "codigoCliente" => $felInvoice->codigoCliente,
            "emailCliente" => $felInvoice->emailCliente,
            "telefonoCliente" => $felInvoice->telefonoCliente,
            "codigoPuntoVenta" => (int) $felInvoice->codigoPuntoVenta,
            "codigoMoneda" => (int) $felInvoice->codigoMoneda,
            "tipoCambio" => round((float)$felInvoice->tipoCambio,2),
            "montoTotal" => $felInvoice->montoTotal,
            "montoTotalMoneda" => $felInvoice->montoTotalMoneda,
            "montoTotalSujetoIva" => $felInvoice->montoTotalSujetoIva,
            "usuario" => $felInvoice->usuario,
            "created_at" => $felInvoice->created_at,
            "updated_at" => $felInvoice->updated_at,
            "cuf" => $felInvoice->cuf,
            "sin_status" => $felInvoice->estado,
            "codigoEstado" => $felInvoice->codigoEstado,
            "sin_errors" => $felInvoice->errores,
            "direccionComprador" => $felInvoice->direccionComprador,
            "concentradoGranel" => $felInvoice->concentradoGranel,
            "origen" => $felInvoice->origen,
            "puertoTransito" => $felInvoice->puertoTransito,
            "incoterm" => $felInvoice->incoterm,
            "puertoDestino" => $felInvoice->puertoDestino,
            "paisDestino" => (int) $felInvoice->paisDestino,
            "tipoCambioANB" => round((float)$felInvoice->tipoCambioANB, 2),
            "numeroLote" => $felInvoice->numeroLote,
            "kilosNetosHumedos" => $felInvoice->kilosNetosHumedos,
            "humedadValor" => $felInvoice->humedadValor,
            "humedadPorcentaje" => $felInvoice->humedadPorcentaje,
            "mermaValor" => $felInvoice->mermaValor,
            "mermaPorcentaje" => $felInvoice->mermaPorcentaje,
            "kilosNetosSecos" => $felInvoice->kilosNetosSecos,
            "gastosRealizacion" => $felInvoice->gastosRealizacion,
            "monedaTransaccional" => $felInvoice->otrosDatos->monedaTransaccional ?? null,
            "fleteInternoUSD" => $felInvoice->otrosDatos->fleteInternoUSD ?? null,
            "valorFobFrontera" => $felInvoice->otrosDatos->valorFobFrontera ?? null,
            "valorPlata" => $felInvoice->otrosDatos->valorPlata ?? null,
            "valorFobFronteraBs" => $felInvoice->otrosDatos->valorFobFronteraBs ?? null,
            "sector_document_type_id" => $felInvoice->type_document_sector_id ?? null,
            "emission_type" => $felInvoice->emission_type,
            "codigo_sucursal" => (int) $felInvoice->codigoSucursal,
            "codigo_pos" => (int) $felInvoice->codigoPuntoVenta,
        ];

    }

}