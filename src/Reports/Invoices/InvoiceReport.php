<?php

namespace EmizorIpx\ClientFel\Reports\Invoices;

use EmizorIpx\ClientFel\Http\Resources\InvoiceReportResource;
use EmizorIpx\ClientFel\Reports\BaseReport;
use Carbon\Carbon;
use EmizorIpx\ClientFel\Reports\ReportInterface;
use Exception;

class InvoiceReport extends BaseReport implements ReportInterface {

    protected $branch_code;

    protected $type_document;

    protected $state;

    protected $group;

    protected $columns;

    protected $company_id;

    protected $user;

    protected $branch_desc = 'Todos';

    const GROUP_BY_CLIENT = 'cliente';

    const GROUP_BY_BRANCH = 'sucursal';



    public function __construct( $company_id, $request, $columns, $user )
    {
        $this->company_id = $company_id;

        $this->branch_code = $request->has('branch_code') ? $request->get('branch_code') : null;

        $this->type_document = $request->has('type_document') ? $request->get('type_document') : null;

        $this->state = $request->has('state') ? $request->get('state') : null;

        $this->group = $request->has('group') ? $request->get('group') : null;

        $from = $request->has('from_date') ? $request->get('from_date') : null;
        $to = $request->has('to_date') ? $request->get('to_date') : null;

        $this->columns = $columns;

        $this->user = $user;

        parent::__construct($from, $to);
        
    }

    public function mapGroupData( $value ) {
        switch ($value) {
            case static::GROUP_BY_BRANCH:
                return 'codigoSucursal';
                break;

            case static::GROUP_BY_CLIENT:
                return 'nombreRazonSocial';
                break;

            default:
                throw new Exception('Atributo no soportado');
                break;
        }
    }

    public function addBranchFilter( $query ) {

        if( !is_null($this->branch_code) ) {

            \Log::debug("Filter by Brach: " . $this->branch_code);

            $this->branch_desc = "Sucursal " . $this->branch_code;

            return $query->where('fel_invoice_requests.codigoSucursal', $this->branch_code);

        } elseif( count($branch_access = $this->user->getOnlyBranchAccess()) > 0 ) {

            $branch_access = $this->user->getOnlyBranchAccess();

            \Log::debug("Filter by Access Branch");

            $branches_desc = [];
            foreach ($branch_access as $value) {
                array_push( $branches_desc, ($value == 0 ? " Casa Matriz" : " Sucursal " . $value) );  
            }

            $this->branch_desc = implode(" - ", $branches_desc);

            return $query->whereIn('fel_invoice_requests.codigoSucursal', $branch_access);

        }

        return $query;
    }


    public function addTypeDocumentFilter ( $query ) {

        if( !is_null( $this->type_document ) ) {

            return $query->where('fel_invoice_requests.type_document_sector_id', $this->type_document);

        }

        return $query;

    }

    public function addStateFilter ( $query ) {

        if( !is_null( $this->state ) && $this->state != 'null' ) {

            \Log::debug("Filter State: " . $this->state);
            return $query->where( 'fel_invoice_requests.codigoEstado', $this->state );
            
        } elseif ( $this->state == 'null' ) {
            
            \Log::debug("Filter State NULL: " . $this->state);
            return $query->whereNull( 'fel_invoice_requests.codigoEstado');

        }

        return $query;

    }

    public function addGroupFilter( $query ) {
        // Grouped by client - branch 
        // TODO: Added query
        if( !is_null($this->group) ) {

            \Log::debug("Group by " . $this->group);
            return $query->groupBy('fel_invoice_requests.' . $this->mapGroupData($this->group) );

        }

        return $query;

    }

    public function addSelectColumns( $query ) {

        foreach( $this->columns as $column) {
            if($column == 'montoTotalVenta'){

                $query->selectRaw('fel_invoice_requests.montoTotal + fel_invoice_requests.descuentoAdicional  as montoTotalVenta');

            } elseif( $column == 'codigoSucursal'){

                $query->selectRaw("IF (fel_invoice_requests.$column = 0, 'CASA MATRIZ', CONCAT('SUCURSAL ', fel_invoice_requests.$column))  as codigoSucursal");

            } else {

                $query->addSelect("fel_invoice_requests.$column");
                
            }
        }

        return $query;

    }

    public function addSelectColumnsToGroup( $query ) {


        $column_group = $this->mapGroupData($this->group);

        foreach( $this->columns as $column) {
            if( ! in_array($column, ['montoTotal', 'descuentoAdicional', $column_group]) ){

                $query->selectRaw("NULL as $column");

            }
        }

        if( $column_group == 'codigoSucursal' ) {
            
            $query->selectRaw("CONCAT( IF (fel_invoice_requests.$column_group = 0, 'CASA MATRIZ', CONCAT('SUCURSAL ', fel_invoice_requests.$column_group)), ' (',COUNT(*),')') as $column_group");

        } else {

            $query->selectRaw("CONCAT( fel_invoice_requests.$column_group, ' (',COUNT(*),')') as $column_group");

        }

        $query->selectRaw('SUM(fel_invoice_requests.montoTotal) as montoTotal');

        $query->selectRaw('SUM(fel_invoice_requests.descuentoAdicional) as descuentoAdicional');

        $query->selectRaw('fel_invoice_requests.montoTotal + fel_invoice_requests.descuentoAdicional  as montoTotalVenta');

        return $query;

    }

    public function generateReport() {

        $query_invoices = \DB::table('invoices')->join('fel_invoice_requests', 'fel_invoice_requests.id_origin', '=' , 'invoices.id')->where('fel_invoice_requests.codigoEstado', 690) ;
        
        if ($this->user && ! $this->user->hasPermission('view_invoice')) {

            \Log::debug("Filter By User: " . $this->user->id);

            $query_invoices = $query_invoices->where('invoices.user_id', '=', $this->user->id);
        }

        $query_invoices = $this->addDateFilter($query_invoices);

        $query_invoices = $this->addBranchFilter($query_invoices);

        $query_invoices = $this->addTypeDocumentFilter($query_invoices);
        
        $query_invoices = $this->addStateFilter($query_invoices);

        $query_invoices = $this->addGroupFilter($query_invoices);

        if( !is_null($this->group) ) {

            $query_invoices = $this->addSelectColumnsToGroup($query_invoices);

        } else {

            $query_invoices =  $this->addSelectColumns($query_invoices);

        }



        
        $query_invoices = $query_invoices->where('fel_invoice_requests.company_id', $this->company_id);
        
        // \Log::debug("SQL Statement: ". $query_invoices->toSql());


        $invoices = $query_invoices->get();

        \Log::debug("Columns to Select: "  . count($invoices));

        \Log::debug("Total Ventas Bs: " . $invoices->sum('montoTotal'));

        return [
            "header" => [
                "sucursal" => $this->branch_desc,
                "usuario" => $this->user->name(),
                "fechaReporte" => Carbon::now()->timezone('America/La_Paz')->toDateTimeString()
            ],
            "totales" =>[
                "montoTotalGeneral" => $invoices->sum('montoTotal')
            ],
            "invoices" => InvoiceReportResource::collection($invoices)->resolve()
        ];

    }

}
