<?php

namespace EmizorIpx\ClientFel\Utils;

use EmizorIpx\ClientFel\Models\BitacoraLog;

class BitacoraType
{

    public static function button($type)
    {
        switch ($type) {
            case BitacoraLog::INFO:
               $color ="info";
                break;
            case BitacoraLog::ERROR:
               $color ="danger";
                break;
            case BitacoraLog::WARNING:
               $color ="warning";
                break;
            case BitacoraLog::REQUEST:
               $color ="success";
                break;
            
            default:
                $color = "dark";
                break;
        }

        return "<span class='badge badge-$color'>$type</span>";
    }
}
