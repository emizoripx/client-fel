<?php

namespace EmizorIpx\ClientFel\Utils;

use Carbon\Carbon;

class InvoiceUtils {

    public static function getFechaEmisionLiteral( $fecha_emision = null ) {

        $month = Carbon::parse( $fecha_emision)->format('F');
        $date_emission = Carbon::parse($fecha_emision)->format('d \of F \of Y h:m:s a');

        \Log::debug("Month Literal: " . $month);
        $literal_date = str_replace($month, trans( 'texts.' . strtolower($month)), $date_emission);

        $literal_date = str_replace('of', 'de', $literal_date);
        return  $literal_date;

    }

}