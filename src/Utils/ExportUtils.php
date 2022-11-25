<?php

namespace EmizorIpx\ClientFel\Utils;

use EmizorIpx\ClientFel\Reports\Clients\ClientsReport;
use EmizorIpx\ClientFel\Reports\Invoices\ComprobanteDiarioUnslpReport;
use EmizorIpx\ClientFel\Reports\Invoices\CosinCantidadReport;
use EmizorIpx\ClientFel\Reports\Invoices\InvoiceReport;
use EmizorIpx\ClientFel\Reports\Invoices\RegisterSalesReport;
use EmizorIpx\ClientFel\Reports\ItemInvoice\InraResumenIngresosReport;
use EmizorIpx\ClientFel\Reports\ItemInvoice\InraTotalesReport;
use EmizorIpx\ClientFel\Reports\ItemInvoice\ItemInvoiceDailyMovementReport;
use EmizorIpx\ClientFel\Reports\ItemInvoice\ItemInvoiceReport;
use EmizorIpx\ClientFel\Reports\Orders\ItemsReport;
use EmizorIpx\ClientFel\Reports\Orders\ItemTurnsReport;
use EmizorIpx\ClientFel\Reports\Products\ProductReport;
use Exception;

class ExportUtils {

    const INVOICE_ENTITY = 'Invoice';
    
    const ITEMS_ENTITY = 'Items';

    const ITEMS_INVOICE_ENTITY = 'Items_Invoice';

    const CLIENTS_ENTITY = 'Clients';

    const QUANTITY_ITEMS = 'Item_Cantidad';

    const ORDER_ITEMS = 'Order_Items';

    const ITEMS_TURNS = 'Items_Turno';

    const INRA_RESUMEN = 'Inra_Resumen';

    const INRA_TOTALES = 'Inra_Totales';

    const REGISTER_SALES = 'Registro_Ventas';

    const COMPROBANTE_DIARIO_CUSTOM1 = 'Comprobante_diario_UNSL';

    const DAILY_MOVEMENTS = 'Movimiento_diario';


    public static function saveFileLocal($name, $datetime, $content, $is_pdf = false) {

        // Here we use the date for unique filename - This is the filename for the View
        $viewfilename = $name."-".hash('sha1', $datetime . md5(rand(1, 1000))). ( $is_pdf ? '.blade.php' : ".xlsx" );

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
        \Log::debug("ENTIDAD  ====> . " . $entity);
        switch ($entity) {

            case static::INVOICE_ENTITY:
                return InvoiceReport::class;
                break;

            case static::ITEMS_ENTITY:
                return ProductReport::class ;
                break;

            case static::ITEMS_INVOICE_ENTITY:
                return ItemInvoiceReport::class ;
                break;
            case static::CLIENTS_ENTITY:
                return ClientsReport::class ;
                break;

            case static::QUANTITY_ITEMS:
                return CosinCantidadReport::class;
                break;
            
            case static::ORDER_ITEMS:
                return ItemsReport::class ;
                break;

            case static::ITEMS_TURNS:
                return ItemTurnsReport::class;
                break;
            
            case static::INRA_RESUMEN:
                return InraResumenIngresosReport::class;
                break;
            
            case static::INRA_TOTALES:
                return InraTotalesReport::class;
                break;

            case static::REGISTER_SALES:
                return RegisterSalesReport::class;
                break;

            case static::COMPROBANTE_DIARIO_CUSTOM1:
                return ComprobanteDiarioUnslpReport::class;
                break;
            case static::DAILY_MOVEMENTS:
                return ItemInvoiceDailyMovementReport::class;
                break;
            
            default:
            \Log::debug("ingresando a este reporte");
                new Exception('Reporte no soportado');
                break;
        }

    }

    public static function flatten_array ( $array ) {
        return array_merge (...$array);
    }

}