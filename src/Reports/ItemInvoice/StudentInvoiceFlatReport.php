<?php

namespace EmizorIpx\ClientFel\Reports\ItemInvoice;

use EmizorIpx\ClientFel\Reports\BaseReport;
use EmizorIpx\ClientFel\Reports\ReportInterface;
use Carbon\Carbon;

class StudentInvoiceFlatReport extends BaseReport implements ReportInterface {

    protected $branch_code;
    protected $columns;
    protected $company_id;
    protected $user;
    protected $branch_desc = "Todos";
    protected $same_user;
    protected $host;
    
    public function __construct( $company_id, $request, $columns, $user )
    {
        $this->company_id = $company_id;
        $this->branch_code = $request->has('branch') ? $request->get('branch') : null;
        $this->from = $request->has('from_date') ? $request->get('from_date') : null;
        $this->to = $request->has('to_date') ? $request->get('to_date') : null;
        $this->columns = $columns;
        $this->same_user = $request->has('same_user') ? ($request->get('same_user') == "true" ? true:false) : false;
        $this->user = $user;
        $this->host = $request->has('host') ? $request->get('host') : env('APP_URL');

        parent::__construct($this->from, $this->to);
    }

    public function addBranchFilter( $query ) {

        if( !is_null($this->branch_code) ) {
            $this->branch_desc = "Sucursal " . $this->branch_code;
            return $query->where('fel_invoice_requests.codigoSucursal', $this->branch_code);
        } elseif( count($branch_access = $this->user->getOnlyBranchAccess()) > 0 ) {
            $branch_access = $this->user->getOnlyBranchAccess();
            $branches_desc = [];
            foreach ($branch_access as $value) {
                array_push( $branches_desc, ($value == 0 ? " Casa Matriz" : " Sucursal " . $value) );  
            }
            $this->branch_desc = implode(" - ", $branches_desc);
            return $query->whereIn('fel_invoice_requests.codigoSucursal', $branch_access);
        }

        return $query;
    }

    public function generateReport()
    {
        ini_set('memory_limit', '512M');
        $query_items = \DB::table('fel_invoice_requests')
            ->leftJoin('invoices', 'invoices.id', 'fel_invoice_requests.id_origin')
            ->leftJoin('clients', 'clients.id', 'invoices.client_id')
            ->where('fel_invoice_requests.company_id', $this->company_id)
            ->whereNotNull('fel_invoice_requests.cuf');

        if ($this->same_user) {
            $query_items = $query_items->where('invoices.user_id', '=', $this->user->id);
        } else {
            if ($this->user && !$this->user->hasPermission('view_invoice')) {
                $query_items = $query_items->where('invoices.user_id', '=', $this->user->id);
            }
        }

        $query_items = $this->addBranchFilter($query_items);
        $query_items = $this->addDateFilter($query_items);

        $invoices = $query_items->selectRaw(\DB::raw(
            '
            fel_invoice_requests.fechaEmision,
            fel_invoice_requests.numeroFactura, 
            fel_invoice_requests.codigoCliente,
            clients.id as client_id,
            fel_invoice_requests.nombreEstudiante,
            clients.custom_value2 as carrera,
            fel_invoice_requests.montoTotal,
            fel_invoice_requests.detalles,
            fel_invoice_requests.estado, 
            fel_invoice_requests.codigoEstado
         '
        ))->get();

        // ensure only valid, include offline invoices
        $invoices = $invoices->filter(function ($item) {
            return !is_null($item->estado) && (is_null($item->codigoEstado) ||  ($item->codigoEstado != 902 && $item->codigoEstado != 691));
        })->values();

        $items_changed = collect($invoices)->map(function ($item) {
            $detalle = json_decode($item->detalles, true);

            $client_hash = (new \App\Models\Client())->encodePrimaryKey($item->client_id);
            $base_url = rtrim($this->host, '/');
            $student_link = '=HYPERLINK("' . $base_url . '/#/clients/' . $client_hash . '", "' . $item->nombreEstudiante . '")';

            $row = [
                "numeroFactura" => $item->numeroFactura,
                "fechaEmision" => Carbon::parse($item->fechaEmision)->format('d/M/Y H:i:s'),
                "codigoCliente" => $item->codigoCliente,
                "nombreEstudiante" => $student_link,
                "carrera" => $item->carrera,
                "montoTotal" => round((float)$item->montoTotal, 2),
            ];

            // Delimitar a 10 detalles como máximo
            $max_details = 10;
            $count = count($detalle);
            $limit = $count > $max_details ? $max_details : $count;

            for ($i = 0; $i < $max_details; $i++) {
                $index = $i + 1;
                if ($i < $limit) {
                    $row["cantidad_$index"] = $detalle[$i]['cantidad'];
                    $row["detalle_$index"] = $detalle[$i]['descripcion'];
                    $row["monto_$index"] = round((float)$detalle[$i]['subTotal'], 2);
                } else {
                    $row["cantidad_$index"] = "";
                    $row["detalle_$index"] = "";
                    $row["monto_$index"] = "";
                }
            }

            return $row;
        })->values()->all();

        $header = [
            "Número Factura",
            "Fecha Emisión",
            "Código Estudiante",
            "Estudiante",
            "Carrera",
            "Monto Total Bs"
        ];

        for ($i = 1; $i <= 10; $i++) {
            $header[] = "CANTIDAD $i";
            $header[] = "Detalle $i";
            $header[] = "Monto $i";
        }

        return [
            "header" => $header,
            "items" => $items_changed
        ];
    }
}
