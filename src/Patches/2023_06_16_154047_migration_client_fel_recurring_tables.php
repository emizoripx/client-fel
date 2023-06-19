<?php

use App\Models\Company;
use App\Models\RecurringInvoice;

class MigrationClientFelRecurringTables
{
    /**
     * Run the patch.
     *
     * @return void
     */
    public function run()
    {

        info("inicio de script sql");
        \DB::statement(\DB::raw("update recurring_invoices left join fel_invoice_requests on fel_invoice_requests.recurring_id_origin = recurring_invoices.id set 
            recurring_invoices.type_document_sector_id = fel_invoice_requests.type_document_sector_id,
            recurring_invoices.codigoMetodoPago = fel_invoice_requests.codigoMetodoPago,
            recurring_invoices.codigoPuntoVenta = fel_invoice_requests.codigoPuntoVenta,
            recurring_invoices.codigoSucursal = fel_invoice_requests.codigoSucursal,
            recurring_invoices.nombreRazonSocial = fel_invoice_requests.nombreRazonSocial,
            recurring_invoices.codigoTipoDocumentoIdentidad = fel_invoice_requests.codigoTipoDocumentoIdentidad,
            recurring_invoices.numeroDocumento = fel_invoice_requests.numeroDocumento,
            recurring_invoices.complemento = fel_invoice_requests.complemento,
            recurring_invoices.emailCliente = fel_invoice_requests.emailCliente,
            recurring_invoices.telefonoCliente = fel_invoice_requests.telefonoCliente,
            recurring_invoices.codigoMoneda = fel_invoice_requests.codigoMoneda,
            recurring_invoices.codigoCliente = fel_invoice_requests.codigoCliente,
            recurring_invoices.codigoLeyenda = fel_invoice_requests.codigoLeyenda,
            recurring_invoices.descuentoAdicional = fel_invoice_requests.descuentoAdicional,
            recurring_invoices.montoGiftCard = fel_invoice_requests.montoGiftCard,
            recurring_invoices.montoTotalSujetoIva = fel_invoice_requests.montoTotalSujetoIva,
            recurring_invoices.montoTotal = fel_invoice_requests.montoTotal,
            recurring_invoices.montoTotalMoneda = fel_invoice_requests.montoTotalMoneda,
            recurring_invoices.tipoCambio = fel_invoice_requests.tipoCambio,
            recurring_invoices.numeroTarjeta = fel_invoice_requests.numeroTarjeta,
            recurring_invoices.codigoActividad = fel_invoice_requests.codigoActividad,
            recurring_invoices.detalles = fel_invoice_requests.detalles
            "));

        $addicional_columns = [
            // 2.- ALQUILER
            [ "periodoFacturado" ],
            // 3.- COMERCIAL-EXPORTACION
            [
                "ruex",
                "nim",
                "puertoDestino",
                "paisDestino",
                "lugarDestino",
                "incoterm_detalle",
                "incoterm",
                "totalGastosNacionalesFob",
                "totalGastosInternacionales",
                "numeroDescripcionPaquetesBultos",
                "informacionAdicional",
                "costosGastosNacionales",
                "costosGastosInternacionales",
                "liquidacion_preliminar",
                "direccionComprador",
                "otrosDatos",
            ],
            // 11.- SECTORES_EDUCATIVOS  
            [
                "nombreEstudiante",
                "periodoFacturado"
            ],
          
        ];

        $documents_available = [1,2,3,11,35];
        foreach ($documents_available as $sector_id) {
            
            switch ($sector_id) {
                case '2':
                    $columns = $addicional_columns[0];
                    break;
                case '3':
                    $columns = $addicional_columns[1];
                    break;
                case '11':
                    $columns = $addicional_columns[2];
                    break;
                
                default:
                    $columns = [];
                    break;
            }
            $this->processInvoices($columns, $sector_id);

        }

        info("termino el script");
        
    }

    public function getRecurringInvoicesWithFelInvoice($additional_columns, $sector_id)
    {
        $chunk_size = 1000;
        $last_id = 0;
        $counter = 0;
        do {
            info("INGRESANDO PARA OBTENER 1000 FACTURAS DESDE ULTIMA FACTURA #" . $last_id . " CANTIDAD " . ( $counter * $chunk_size ) . " SECTOR ".$sector_id);
            $recurring_invoices = RecurringInvoice::addSelect('recurring_invoices.id', 'recurring_invoices.line_items', 'recurring_invoices.data_specific_by_sector')
            ->with(['fel_invoice' => function ($query) use ($additional_columns ) {
                $query->select('id', 'numeroDocumento', 'recurring_id_origin', 'detalles');
                $query->addSelect($additional_columns);
            }])
            ->whereHas('fel_invoice', function ($query) use($sector_id) {
                $query->whereNotNull('recurring_id_origin');
                $query->where('type_document_sector_id', $sector_id);
            })
            ->where('recurring_invoices.id', '>', $last_id)
            ->orderBy('recurring_invoices.id')
            ->limit($chunk_size)
            ->get();

            if ($recurring_invoices->count() == 0) {
                break;
            }

            $last_id = $recurring_invoices->last()->id;

            foreach ($recurring_invoices as $rinvoice) {
                yield $rinvoice;
            }
            $counter ++;
        } while (true);
    }

    public function processInvoices($additional_columns, $sector_id)
    {
        foreach ($this->getRecurringInvoicesWithFelInvoice($additional_columns, $sector_id) as $modelInvoice) {
            $fel_invoice = $modelInvoice->fel_invoice;

            if (isset($fel_invoice) && !is_null($fel_invoice)) {
                $data_specific_by_sector = isset($fel_invoice->data_specific_by_sector) ? $fel_invoice->data_specific_by_sector : [];

                foreach ($additional_columns as $ac) {
                    $data_specific_by_sector[$ac] = $fel_invoice->{$ac};
                }

                $modelInvoice->data_specific_by_sector = $data_specific_by_sector;
                $modelInvoice->detalles = collect($modelInvoice->line_items)->merge($fel_invoice->detalles);
                $modelInvoice->saveQuietly();
            }
        }
    }

}
