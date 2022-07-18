<?php

namespace EmizorIpx\ClientFel\Utils;

use EmizorIpx\ClientFel\Reports\Invoices\InvoiceReport;
use Exception;

class ExportUtils {

    const INVOICE_ENTITY = 'Invoice';

    public static function saveFileLocal($name, $datetime, $content) {

        // Here we use the date for unique filename - This is the filename for the View
        $viewfilename = $name."-".hash('sha1', $datetime . md5(rand(1, 1000))).".xlsx";

        // Full path with filename
        $fullfilename = storage_path("app/templates/$viewfilename");

        if( ! is_dir(storage_path('app/templates')) ) {
            \Log::debug("Create diretory Templates");
            mkdir(storage_path('app/templates'));
        }

        // Write the string into a file
        if (!file_exists($fullfilename))
        {
            file_put_contents($fullfilename, $content);
        }

        // Return the view filename - This could be directly used in View::make
        return $fullfilename;

    }


    public static function getClassReport ( $entity ) {

        switch ($entity) {

            case static::INVOICE_ENTITY:
                return InvoiceReport::class;
                break;
            
            default:
                new Exception('Reporte no soportado');
                break;
        }

    }

}