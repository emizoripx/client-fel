<?php
namespace EmizorIpx\ClientFel\Utils;


class TypeParametrics {

    const MOTIVO_ANULACION="motivos-de-anulacion";
    const PAISES="paises";
    const TIPOS_DOCUMENTO_IDENTIDAD="tipos-documento-de-identidad";
    const METODOS_DE_PAGO="metodos-de-pago";
    const MONEDAS="monedas";
    const UNIDADES="unidades";
    const ACTIVIDADES="actividades";
    const LEYENDAS="leyendas";
    const PRODUCTOS_SIN="productos-sin";


    public static function getAll()
    {
        return [
            SELF::MOTIVO_ANULACION,
            SELF::PAISES,
            SELF::TIPOS_DOCUMENTO_IDENTIDAD,
            SELF::METODOS_DE_PAGO,
            SELF::MONEDAS,
            SELF::UNIDADES,
            SELF::ACTIVIDADES,
            SELF::LEYENDAS,
            SELF::PRODUCTOS_SIN
        ];
    }
}