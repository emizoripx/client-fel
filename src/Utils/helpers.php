<?php

use EmizorIpx\ClientFel\Models\BitacoraLog;
use EmizorIpx\ClientFel\Models\FelInvoiceStatusHistorial;
use EmizorIpx\ClientFel\Models\Parametric\Country;
use EmizorIpx\ClientFel\Utils\Currencies;
use EmizorIpx\ClientFel\Utils\FunctionUtils;
use EmizorIpx\ClientFel\Utils\InvoiceUtils;
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
if(!function_exists('country')){
    function country($codigo){
        return Country::getDescriptionCountry($codigo);
    }
}

if(!function_exists('emission_date_to_literal')){
    function emission_date_to_literal($fecha_emision = null){
        return InvoiceUtils::getFechaEmisionLiteral( $fecha_emision);
    }
}

if(!function_exists('short_emission_date_to_literal')){
    function short_emission_date_to_literal($fecha_emision = null){
        return InvoiceUtils::getFechaEmisionLiteralFormato2( $fecha_emision);
    }
}

// if(!function_exists('match_data')){
//     function match_data($value, $options){
//         return FunctionUtils::match_value($value, $options);
//     }
// }


if (!function_exists('cobrosqr_logging')) {
    function cobrosqr_logging($output, $context = []): void
    {
        if (gettype($output) == 'object') {
            $output = print_r($output, 1);
        }
        \Illuminate\Support\Facades\Log::channel('cobroqr_logs')->info($output, $context);
    }
}