<?php

namespace EmizorIpx\ClientFel\Utils;

use App\Models\DateFormat;
use App\Models\RecurringInvoice;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Carbon\Carbon;

class FunctionUtils {

    public static function match_value( $value, $options ){

        return $options[$value];

    }

    public static function updateAddicionalData( $feldata, $frequency_id = null ) {
        
        $fel_data_temp = $feldata['felData']->resolve();

        $sector_document = $fel_data_temp['sector_document_type_id'];

        \Log::debug("Update Periodo ENTER 3: "  . $sector_document);

        switch ($sector_document) {
            case TypeDocumentSector::ALQUILER_BIENES_INMUEBLES:
                $date = Carbon::now();

                \Log::debug("Periodo Update to : " . trans( 'texts.' . strtolower(DateFormat::$months_of_the_years[$date->month - 1 ]))  . ' - ' . $date->year);

                $fel_data_temp['periodoFacturado'] = trans( 'texts.' . strtolower(DateFormat::$months_of_the_years[$date->month - 1 ])) . ' - ' . $date->year ;

                break;
            
            case TypeDocumentSector::SECTORES_EDUCATIVOS:
                if ( $frequency_id == RecurringInvoice::FREQUENCY_MONTHLY ) {

                    $date = Carbon::now();
    
                    \Log::debug("Periodo Update to : " . trans( 'texts.' . strtolower(DateFormat::$months_of_the_years[$date->month - 1 ]))  . ' - ' . $date->year);
    
                    $fel_data_temp['periodoFacturado'] = trans( 'texts.' . strtolower(DateFormat::$months_of_the_years[$date->month - 1 ])) . ' - ' . $date->year ;

                }
            
            default:
                \Log::debug("Return FEL Data Default");
                break;
        }

        return ['felData' => $fel_data_temp];

    }

    public static function updateTermsTemplate($termsTemplate)
    {
        // regular expresion to extract information between [[ and ]]
        $patron = '/\[\[(.*?)\]\]/';
        $template = null;
        if (preg_match($patron, $termsTemplate, $coincidences)) {
            // found information
            $template = $coincidences[1];
        } 
        $period_changed = "";
        switch ($template) {
            case 'MENSUAL_PREPAGO':
                $period_changed = static::literalMonthsPeriod(now()->format("m"))  . "-" . now()->format("Y");
                break;
            case 'MENSUAL_POSTPAGO':
                $period_changed = static::literalMonthsPeriod(now()->subMonth()->format("m"))   . "-" . now()->format("Y");
                break;
            default:
                $period_changed = "";
                break;
        }

        if ($period_changed != "") {
            // replace
            return preg_replace($patron, $period_changed, $termsTemplate);
        }

        return $termsTemplate;

    }

    public static function literalMonthsPeriod($num)
    {
        $months = ["","ENERO", "FEBRERO","MARZO", "ABRIL", "MAYO", "JUNIO", "JULIO", "AGOSTO", "SEPTIEMBRE", "OCTUBRE", "NOVIEMBRE", "DICIEMBRE"];

        return $months[intval($num)];
    }

}