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
    const TIPOS_DOCUMENTO_SECTOR = "tipos-documento-sector";
    const ACTIVIDADES_DOCUMENTO_SECTOR = "actividades-documento-sector";
    const TIPOS_HABITACION = 'tipo-habitacion';


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
            SELF::PRODUCTOS_SIN,
            SELF::TIPOS_DOCUMENTO_SECTOR,
            SELF::ACTIVIDADES_DOCUMENTO_SECTOR,
            SELF::TIPOS_HABITACION
        ];
    }
}