<?php

use EmizorIpx\ClientFel\Models\BitacoraLog;

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