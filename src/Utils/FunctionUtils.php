<?php

namespace EmizorIpx\ClientFel\Utils;

use App\Models\DateFormat;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Carbon\Carbon;

class FunctionUtils {

    public static function match_value( $value, $options ){

        return $options[$value];

    }

    public static function updateAddicionalData( $feldata ) {
        
        $fel_data_temp = $feldata['felData']->resolve();

        $sector_document = $fel_data_temp['sector_document_type_id'];

        \Log::debug("Update Periodo ENTER 3: "  . $sector_document);

        switch ($sector_document) {
            case TypeDocumentSector::ALQUILER_BIENES_INMUEBLES:
                $date = Carbon::now();

                \Log::debug("Periodo Update to : " . trans( 'texts.' . strtolower(DateFormat::$months_of_the_years[$date->month - 1 ]))  . ' - ' . $date->year);

                $fel_data_temp['periodoFacturado'] = trans( 'texts.' . strtolower(DateFormat::$months_of_the_years[$date->month - 1 ])) . ' - ' . $date->year ;

                break;
            
            default:
                \Log::debug("Return FEL Data Default");
                break;
        }

        return ['felData' => $fel_data_temp];

    }

}