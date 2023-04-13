<?php

namespace EmizorIpx\ClientFel\Reports\Products;

use EmizorIpx\ClientFel\Models\FelInvoiceRequest;
use EmizorIpx\ClientFel\Reports\BaseReport;
use EmizorIpx\ClientFel\Reports\ReportInterface;
use EmizorIpx\ClientFel\Utils\ExportUtils;
use EmizorIpx\ClientFel\Utils\NumberUtils;
use Carbon\Carbon;
use Hashids\Hashids;
use EmizorIpx\ClientFel\Http\Resources\ItemResumeResource;

class UNSDLPProductReport extends BaseReport implements ReportInterface {

    protected $branch_code;

    protected $columns;

    protected $company_id;

    protected $user;

    protected $branch_desc = "Todos";

    protected $all_users;
    
    protected $user_selected;

    public function __construct( $company_id, $request, $columns, $user )
    {
        $this->company_id = $company_id;

        $this->branch_code = $request->has('branch') ? $request->get('branch') : null;

        $from = $request->has('from_date') ? $request->get('from_date') : null;
        $to = $request->has('to_date') ? $request->get('to_date') : null;

        $this->columns = $columns;

        $this->user_selected = null;

        $this->all_users = $request->has('all_users') ? ($request->get('all_users') == "true" ? true : false) : false;
        $hashid = new Hashids(config('ninja.hash_salt'), 10);
        
        $this->user = $user;
        if (!$this->all_users) {

            $user_selected = $request->has('user') ? ( !empty($request->get('user')) ? $hashid->decode( $request->get('user') ) : null) : null;
            $cu = null;
            if (!is_null($user_selected)) {
                $cu = \App\Models\CompanyUser::whereUserId($user_selected)->whereCompanyId($company_id)->first();
                \Log::debug("user selected ID = " , $user_selected);
            }
            if (!empty($cu) && !is_null($cu)) {
                $this->user_selected = $cu;
            }
        }

        parent::__construct($from, $to);
        
    }

    public function addBranchFilter( $query ) {

        if( !is_null($this->branch_code)) {

            \Log::debug("Filter by Brach: " . gettype($this->branch_code));

            $this->branch_desc =($this->branch_code == 0 ? " Casa Matriz" : " Sucursal " . $this->branch_code);

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


    public function generateReport () {

        $query_items = \DB::table('invoices')->join('fel_invoice_requests', 'invoices.id', '=', 'fel_invoice_requests.id_origin')->where('fel_invoice_requests.company_id', $this->company_id)->where('fel_invoice_requests.codigoEstado', 690);

        if (!$this->all_users) {
            
            if (!is_null($this->user_selected)) {
                \Log::debug("using user select _id : " . $this->user_selected->user_id);
                $query_items = $query_items->where('invoices.user_id', '=', $this->user_selected->user_id);
                
            }else if ($this->user && ! $this->user->hasPermission('view_invoice')) {
                
                \Log::debug("Filter By User: " . $this->user->id);
                
                $query_items = $query_items->where('invoices.user_id', '=', $this->user->id);

            }
        }
        
        $query_items = $this->addDateFilter($query_items);

        $query_items = $this->addBranchFilter($query_items);

        // Has Permission

        $items_array = $query_items->pluck('fel_invoice_requests.detalles');

        $items_array_dec = json_decode($items_array);

        $items_array = collect($items_array_dec)->map( function ( $detail ) {

            return json_decode($detail, true);
        })->all();

        $items = ExportUtils::flatten_array($items_array);

        $items_grouped = collect($items)->groupBy('codigoProducto')->all();

        $data = collect($items_grouped)->map( function ( $item, $key ) {

            \Log::debug("Key: " . $key);

            $cantidad = collect($item)->sum('cantidad');
            $subTotal = collect($item)->sum('subTotal');
            $montoDescuento = collect($item)->sum('montoDescuento');

            \Log::debug("Cantidad Vendido: " . $cantidad);

            $item_m = [
                'cantidad' => $cantidad,
                'codigoProducto' => $key,
                'descripcion' => $item[0]['descripcion'],
                'precioUnitario' => $item[0]['precioUnitario'],
                'montoDescuento' => $montoDescuento,
                'subTotal' => $subTotal
            ];

            $item = $item_m;

            return $item;

        })->values();

        return [
            "header" => [
                "sucursal" => $this->branch_desc,
                "usuario" => $this->all_users ? 'Todos' : $this->user_selected->user->name() ,
                "by_usuario" => $this->user->name(),
                "fechaReporte" => Carbon::now()->timezone('America/La_Paz')->toDateTimeString()
            ],
            "totales" =>[
                "montoTotalGeneral" => NumberUtils::number_format_custom(collect($data)->sum('subTotal'), 2)
            ],
            "items" => ItemResumeResource::collection($data)->resolve()
        ];

    }

    
}
