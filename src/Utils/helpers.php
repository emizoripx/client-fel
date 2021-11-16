<?php

use EmizorIpx\ClientFel\Models\BitacoraLog;
use EmizorIpx\ClientFel\Models\FelInvoiceStatusHistorial;
use EmizorIpx\ClientFel\Utils\Currencies;
use EmizorIpx\ClientFel\Utils\NumberToWord;

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

if(!function_exists('to_word')){
    function to_word($number, $decimal, $codigoMoneda){
        return NumberToWord::getToWord($number, $decimal, Currencies::getDescriptionCurrency($codigoMoneda));
    }
}
if(!function_exists('currency_description')){
    function currency_description($codigoMoneda){
        return Currencies::getSingularDescriptionCurrency($codigoMoneda);
    }
}
if(!function_exists('currency_description_plural')){
    function currency_description_plural($codigoMoneda){
        return Currencies::getDescriptionCurrency($codigoMoneda);
    }
}