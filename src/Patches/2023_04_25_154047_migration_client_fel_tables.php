<?php

use App\Models\Invoice;

class MigrationClientFelTables
{
    /**
     * Run the patch.
     *
     * @return void
     */
    public function run()
    {

        // info("inicio de script sql");
        // \DB::statement(\DB::raw("update invoices left join fel_invoice_requests on fel_invoice_requests.id_origin = invoices.id set 
        //     invoices.factura_ticket = fel_invoice_requests.factura_ticket,
        //     invoices.numeroFactura = fel_invoice_requests.numeroFactura,
        //     invoices.type_document_sector_id = fel_invoice_requests.type_document_sector_id,
        //     invoices.type_invoice = fel_invoice_requests.type_invoice,
        //     invoices.codigoMetodoPago = fel_invoice_requests.codigoMetodoPago,
        //     invoices.fechaEmision = fel_invoice_requests.fechaEmision,
        //     invoices.codigoPuntoVenta = fel_invoice_requests.codigoPuntoVenta,
        //     invoices.codigoSucursal = fel_invoice_requests.codigoSucursal,
        //     invoices.nombreRazonSocial = fel_invoice_requests.nombreRazonSocial,
        //     invoices.codigoTipoDocumentoIdentidad = fel_invoice_requests.codigoTipoDocumentoIdentidad,
        //     invoices.numeroDocumento = fel_invoice_requests.numeroDocumento,
        //     invoices.complemento = fel_invoice_requests.complemento,
        //     invoices.emailCliente = fel_invoice_requests.emailCliente,
        //     invoices.telefonoCliente = fel_invoice_requests.telefonoCliente,
        //     invoices.cafc = fel_invoice_requests.cafc,
        //     invoices.typeDocument = fel_invoice_requests.typeDocument,
        //     invoices.codigoExcepcion = fel_invoice_requests.codigoExcepcion,
        //     invoices.codigoMoneda = fel_invoice_requests.codigoMoneda,
        //     invoices.codigoCliente = fel_invoice_requests.codigoCliente,
        //     invoices.codigoLeyenda = fel_invoice_requests.codigoLeyenda,
        //     invoices.usuario = fel_invoice_requests.usuario,
        //     invoices.extras = fel_invoice_requests.extras,
        //     invoices.numeroAutorizacionCuf = fel_invoice_requests.numeroAutorizacionCuf,
        //     invoices.factura_original_id = fel_invoice_requests.factura_original_id,
        //     invoices.facturaExterna = fel_invoice_requests.facturaExterna,
        //     invoices.descuentoAdicional = fel_invoice_requests.descuentoAdicional,
        //     invoices.montoGiftCard = fel_invoice_requests.montoGiftCard,
        //     invoices.montoTotalSujetoIva = fel_invoice_requests.montoTotalSujetoIva,
        //     invoices.montoTotal = fel_invoice_requests.montoTotal,
        //     invoices.montoTotalMoneda = fel_invoice_requests.montoTotalMoneda,
        //     invoices.tipoCambio = fel_invoice_requests.tipoCambio,
        //     invoices.montoDescuentoCreditoDebito = fel_invoice_requests.montoDescuentoCreditoDebito,
        //     invoices.montoEfectivoCreditoDebito = fel_invoice_requests.montoEfectivoCreditoDebito,
        //     invoices.numeroTarjeta = fel_invoice_requests.numeroTarjeta,
        //     invoices.detalles = fel_invoice_requests.detalles,
        //     invoices.cuf = fel_invoice_requests.cuf,
        //     invoices.codigoActividad = fel_invoice_requests.codigoActividad,
        //     invoices.codigoEstado = fel_invoice_requests.codigoEstado,
        //     invoices.estado = fel_invoice_requests.estado,
        //     invoices.errores = fel_invoice_requests.errores,
        //     invoices.revocation_reason_code = fel_invoice_requests.revocation_reason_code,
        //     invoices.urlSin = fel_invoice_requests.urlSin,
        //     invoices.search_fields = fel_invoice_requests.search_fields,
        //     invoices.external_invoice_data = fel_invoice_requests.external_invoice_data,
        //     invoices.emitted_by = fel_invoice_requests.emitted_by,
        //     invoices.revocated_by = fel_invoice_requests.revocated_by,
        //     invoices.ack_ticket = fel_invoice_requests.ack_ticket,
        //     invoices.package_id = fel_invoice_requests.package_id,
        //     invoices.index_package = fel_invoice_requests.index_package,
        //     invoices.uuid_package = fel_invoice_requests.uuid_package
        //     "));


        // \DB::statement(
        //     \DB::raw(
        //         "
        //     update clients left join fel_clients on fel_clients.id_origin = clients.id set 
        //     clients.document_number = fel_clients.document_number,
        //     clients.business_name = fel_clients.business_name,
        //     clients.type_document_id = fel_clients.type_document_id,
        //     clients.complement = fel_clients.complement,
        //     clients.search_fields = fel_clients.search_fields
        //     "
        //     )
        // );

        // // // transfer products
        // \DB::statement(
        //     \DB::raw(
        //         "
        //     update products left join fel_sync_products on fel_sync_products.id_origin = products.id set 
        //     products.codigo_producto = fel_sync_products.codigo_producto,
        //     products.codigo_actividad_economica = fel_sync_products.codigo_actividad_economica,
        //     products.codigo_unidad = fel_sync_products.codigo_unidad,
        //     products.nombre_unidad = fel_sync_products.nombre_unidad,
        //     products.codigo_producto_sin = fel_sync_products.codigo_producto_sin,
        //     products.codigo_nandina = fel_sync_products.codigo_nandina
        //     "
        //     )
        // );
        // info("fin de script sql");

        $common = [
            "direccion",
            "deleted_at",
            "type_invoice_id",
            "xml_url",
            "emission_type"
        ];
        $addicional_columns = [
            [],
            // 1.- COMPRA-VENTA
            $common,
            // 2.- ALQUILER
            array_merge($common,
            [
                "periodoFacturado"
            ]),
            // 3.- COMERCIAL-EXPORTACION
            array_merge($common,
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
            ]),
            // 4.- COMERCIAL_EXPORTACION_LIBRE_CONSIGNACION
            array_merge(
                $common,
                []
            ),
            // 5.- ZONA_FRANCA
            array_merge(
                $common,
                [
                    "numeroParteRecepcion"
                ]
            ),
            // 6.- SERVICIO_TURISTICO_HOSPEDAJE
            array_merge(
                $common,
                [
                    // "razonSocialOperadorTurismo"
                ]
            ),
            // 7.- COMERCIALIZACION_ALIMENTOS_SEGURIDAD
            $common,
            // 8.- TASA_CERO  
            $common,
            // 9.- COMPRA_VENTA_MONEDA_EXTRANJERA =
            $common,
            // 10.- DUTTY_FREE =
            $common,
            // 11.- SECTORES_EDUCATIVOS  
            array_merge($common,[
                "nombreEstudiante",
                "periodoFacturado"
            ]),
            // 12.- COMERCIALIZACION_HIDROCARBUROS  
            array_merge($common,[
                "placaVehiculo", 
                "tipoEnvase",
                "codigoAutorizacionSC",
                "observacion",
            ]),
            // 13.- SERVICIOS_BASICOS  
            array_merge($common,[
                "mes",
                "gestion",
                "ciudad",
                "zona",
                "numeroMedidor",
                "domicilioCliente",
                "consumoPeriodo",
                "beneficiarioLey1886",
                "montoDescuentoLey1886",
                "montoDescuentoTarifaDignidad",
                "tasaAseo",
                "tasaAlumbrado",
                "ajusteNoSujetoIva",
                "detalleAjusteNoSujetoIva",
                "ajusteSujetoIva",
                "detalleAjusteSujetoIva",
                "otrosPagosNoSujetoIva",
                "detalleOtrosPagosNoSujetoIva",
                "otrasTasas"
            ]),
            // 14.- PRODUCTOS_ALCANZADOS_ICE  
            array_merge($common,[
                // "montoIceEspecifico",
                // "montoIcePorcentual"
            ]),
            // 15.- ENTIDADES_FINANCIERAS  
            array_merge($common,[
                "montoTotalArrendamientoFinanciero"
            ]),
            // 16.- HOTELES  
            array_merge($common,[
                "cantidadHuespedes",
                "cantidadHabitaciones",
                "cantidadMayores",
                "cantidadMenores",
                "fechaIngresoHospedaje",
            ]),
            // 17.- HOSPITALES_CLINICAS  
            $common,
            // 18.- JUEGOS_AZAR  
            $common,
            // 19.- HIDROCARBUROS_IEHD  
            array_merge($common,[
                "ciudad",
                "nombrePropietario",
                "nombreRepresentanteLegal",
                "condicionPago",
                "periodoEntrega",
                "montoIehd",
            ]),
            // 20.- EXPORTACION_MINERALES  
            array_merge($common,[
                "lugarDestino",
                "direccionComprador",
                "concentradoGranel",
                "origen",
                "puertoTransito",
                "incoterm",
                "puertoDestino",
                "paisDestino",
                "tipoCambioANB",
                "numeroLote",
                "kilosNetosHumedos",
                "humedadValor",
                "humedadPorcentaje",
                "mermaValor",
                "mermaPorcentaje",
                "kilosNetosSecos",
                "gastosRealizacion",
                "pesoBrutoGr",
                "pesoBrutoKg",
                "pesoNetoGr",
                "numeroContrato",
                "otrosDatos",
            ]),
            // 21.- VENTA_INTERNA_MINERALES  
            array_merge($common,[
                "ruex",
                "nim",
                "direccionComprador",
                "concentradoGranel",
                "origen",
                "puertoTransito",
                "incoterm",
                "puertoDestino",
                "paisDestino",
                "tipoCambioANB",
                "numeroLote",
                "kilosNetosHumedos",
                "humedadValor",
                "humedadPorcentaje",
                "mermaValor",
                "mermaPorcentaje",
                "kilosNetosSecos",
                "gastosRealizacion",
                "liquidacion_preliminar",
                "iva",
                "otrosDatos",
            ]),
            // 22.- TELECOMUNICACIONES  
            array_merge($common,[
                "nitConjunto"
            ]),
            // 23.- PREVALORADA  
            $common,
            // 24.- DEBITO_CREDITO  
            $common,
            // 25.- PRODUCTOS_NACIONALES  
            $common,
            // 26.- PRODUCTOS_NACIONALES_ICE  
            $common,
            // 27.- REGIMEN_7RG  
            $common,
            // 28.- COMERCIAL_EXPORTACION_SERVICIOS  
            $common,
            // 29.- NOTA_CONCILIACION  
            array_merge($common,[
                "debitoFiscalIva",
                "creditoFiscalIva",
            ]),
            // 30,31,32,33 UNKNOWN
            $common, $common, $common, $common,
            // 34.- SEGUROS  
            array_merge($common,[
                "ajusteAfectacionIva"
            ]),
            // 35.- COMPRA_VENTA_BONIFICACIONES  
            $common,
            // 36.- PREVALORADA_SDCF  
            $common,
            // 37.- COMERCIALIZACION_GNV  
            array_merge($common,[
                "placaVehiculo",
                "tipoEnvase",
                "montoVale",
            ]),
            // 38.- HIDROCARBUROS_NO_IEHD  
            $common,
            // 39, 40, 41, 42, 43, 44, 45 UNKNOWN
            $common, $common, $common, $common, $common, $common, $common,
            // 46.- SECTOR_EDUCATIVO_ZONA_FRANCA  
            $common,
            // 47, 48, 49, 50 UNKNOWN
            $common, $common, $common, $common,
            // 51.- ENGARRAFADORAS  
            $common,
        ];

        // $documents_available = [1, 2, 3, 5, 6, 8, 11, 14, 17, 20, 21, 22, 24, 29, 35];
        $documents_available = [ 21, 22, 24, 29, 35];
        foreach ($documents_available as $sector_id) {

            $this->processInvoices($addicional_columns, $sector_id);

        }


        info("termino el script");
        
    }

    public function getInvoicesWithFelInvoice($additional_columns, $sector_id)
    {
        $chunk_size = 1000;
        $last_id = 0;
        $counter = 0;
        do {
            info("INGRESANDO PARA OBTENER 1000 FACTURAS DESDE ULTIMA FACTURA #" . $last_id . " CANTIDAD " . ( $counter * $chunk_size ) . " SECTOR ".$sector_id);
            $invoices = Invoice::addSelect('invoices.id', 'invoices.line_items', 'invoices.data_specific_by_sector')
            ->with(['fel_invoice' => function ($query) use ($additional_columns, $sector_id ) {
                $query->select('id', 'numeroDocumento', 'id_origin', 'detalles');
                $query->addSelect($additional_columns[$sector_id]);
            }])
            ->whereHas('fel_invoice', function ($query) use($sector_id) {
                $query->whereNull('recurring_id_origin');
                $query->where('type_document_sector_id', $sector_id);
            })
            ->where('invoices.id', '>', $last_id)
            ->orderBy('invoices.id')
            ->limit($chunk_size)
            ->get();

            if ($invoices->count() == 0) {
                break;
            }

            $last_id = $invoices->last()->id;

            foreach ($invoices as $invoice) {
                yield $invoice;
            }
            $counter ++;
        } while (true);
    }

    public function processInvoices($additional_columns, $sector_id)
    {
        foreach ($this->getInvoicesWithFelInvoice($additional_columns, $sector_id) as $modelInvoice) {
            $fel_invoice = $modelInvoice->fel_invoice;

            if (isset($fel_invoice) && !is_null($fel_invoice)) {
                $data_specific_by_sector = isset($fel_invoice->data_specific_by_sector) ? $fel_invoice->data_specific_by_sector : [];

                foreach ($additional_columns[$sector_id] as $ac) {
                    $data_specific_by_sector[$ac] = $fel_invoice->{$ac};
                }

                $modelInvoice->data_specific_by_sector = $data_specific_by_sector;
                $modelInvoice->detalles = collect($modelInvoice->line_items)->merge($fel_invoice->detalles);
                $modelInvoice->saveQuietly();
            }
        }
    }

}
