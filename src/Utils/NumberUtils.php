<?php

namespace EmizorIpx\ClientFel\Utils;

class NumberUtils {

    public static function format($number, $decimal_separator = '.', $thousand_separator = null, $decimal_precision = 2)
    {
        if (is_null($number)) {
            return $number;
        }
        return number_format($number, $decimal_precision, $decimal_separator, $thousand_separator ?? '');
    }

    public static function format_punto_decimal($number,$decimal_precision=2){
        return self::format($number,'.',',',$decimal_precision);
    }
    public static function format_comma_decimal($number,$decimal_precision=2)
    {
        return self::format($number, ',', '.',$decimal_precision);
    }

    public static function number_format_custom( $number, $decimal_precision, $field = 'default' ){

        return self::format_punto_decimal($number, $decimal_precision);
    }

}