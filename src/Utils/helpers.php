<?php

use EmizorIpx\ClientFel\Models\BitacoraLog;
use EmizorIpx\ClientFel\Models\FelInvoiceStatusHistorial;

if (!function_exists('bitacora_info')) {
    function bitacora_info($event, $message)
    {
        BitacoraLog::register(BitacoraLog::INFO,$event,$message);
    }
}
if (!function_exists('bitacora_error')) {
    function bitacora_error($event, $message)
    {
        BitacoraLog::register(BitacoraLog::ERROR,$event,$message);
    }
}
if (!function_exists('bitacora_request')) {
    function bitacora_request($event, $message)
    {
        BitacoraLog::register(BitacoraLog::REQUEST,$event,$message);
    }
}
if (!function_exists('bitacora_warning')) {
    function bitacora_warning($event, $message)
    {
        BitacoraLog::register(BitacoraLog::WARNING,$event,$message);
    }
}
if (!function_exists('fel_register_historial')) {
    function fel_register_historial($felInvoiceRequest, $errors = null, $codigoRecepcion = null)
    {
        FelInvoiceStatusHistorial::registerHistorialInvoice($felInvoiceRequest, $errors, $codigoRecepcion);
    }
}