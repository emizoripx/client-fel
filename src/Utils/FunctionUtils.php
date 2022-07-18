<?php

namespace EmizorIpx\ClientFel\Utils;

use PhpOffice\PhpSpreadsheet\Style\Alignment;

class FunctionUtils {

    public static function match_value( $value, $options ){

        return $options[$value];

    }

}