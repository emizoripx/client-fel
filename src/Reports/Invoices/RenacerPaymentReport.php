<?php

namespace EmizorIpx\ClientFel\Reports\Invoices;

use EmizorIpx\ClientFel\Reports\BaseReport;
use EmizorIpx\ClientFel\Reports\ReportInterface;
use App\Models\Company;
use Carbon\Carbon;

class RenacerPaymentReport extends BaseReport implements ReportInterface
{
    protected $branch_code;

    protected $type_document;

    protected string $company_id;

    protected array $columns;

    protected $user;

    protected $branch_desc = 'Todos';

    public function __construct(string $company_id, $request,  array $columns, $user)
    {
        $this->company_id = $company_id;

        $this->branch_code = $request->has('branch_code') ? $request->get('branch_code') : null;

        /* $this->type_document = $request->has('type_document') ? $request->get('type_document') : null; */

        $this->columns = $columns;

        $this->user = $user;

        $from = $request->has('from_date') ? $request->get('from_date') : null;
        $to = $request->has('to_date') ? $request->get('to_date') : null;

        parent::__construct(from_date: $from, to_date: $to);
    }

    public function addBranchFilter($query)
    {
        if (!is_null($this->branch_code)) {

            \Log::debug("Filter by Brach: " . $this->branch_code);

            $this->branch_desc = "Sucursal " . $this->branch_code;

            return $query->where('fel_invoice_requests.codigoSucursal', $this->branch_code);
        } elseif (count($branch_access = $this->user->getOnlyBranchAccess()) > 0) {

            $branch_access = $this->user->getOnlyBranchAccess();

            \Log::debug("Filter by Access Branch");

            $branches_desc = [];
            foreach ($branch_access as $value) {
                array_push($branches_desc, ($value == 0 ? " Casa Matriz" : " Sucursal " . $value));
            }

            $this->branch_desc = implode(" - ", $branches_desc);

            return $query->whereIn('fel_invoice_requests.codigoSucursal', $branch_access);
        }

        return $query;
    }

    public function generateReport()                                                                        
    {                                                                                                       
                                                                                                            
        $from = date('Y-m-d', $this->from_date)." 00:00:00";                                                
        $to = date("Y-m-d", $this->to_date). " 23:59:59";                                                   
                                                                                                            
        $query_invoices = \DB::table('invoices')                                                            
            ->leftJoin('users', 'users.id', '=', 'invoices.user_id')                                        
            ->where('status_id',"=", 4)                                                                     
            ->where('company_id', $this->company_id)                                                        
            ->whereNotNull('document_data')                                                                 
            ->whereBetween('date', [$from, $to]);                                                           
                                                                                                            
        $query_invoices->select(                                                                            
            'users.first_name as collector_name',                                                           
            'users.id as user_id',                                                                          
            'document_data'                                                                                 
        );                                                                                         
                                                                                                
        $results_cursor = $query_invoices->cursor();                                               
                                                                                                
        $all_users_cursor = \DB::table('users')                                                    
            ->join('company_user', 'users.id', '=', 'company_user.user_id')                        
            ->where('company_user.company_id', $this->company_id)                                  
            ->select('users.id', 'users.first_name')                                               
            ->cursor();                                                                            
                                                                                                
        return $this->formatReportData($results_cursor, $all_users_cursor);                        
    } 

    private function formatReportData($results_cursor, $all_users_cursor)
    {
        $collectors = [];
        $total_sus = 0;
        $total_bs = 0;

        // Inicializar collectors para todos los usuarios
        foreach ($all_users_cursor as $user) {
            $collector_key = 'cob' . $user->id;
            $collectors[$collector_key] = [
                'name' => $user->first_name,
                'items' => [],
                'subtotal' => [
                    'total_sus' => 0,
                    'total_bs' => 0
                ]
            ];
        }
        $company = Company::find($this->company_id);
        $company_name = $company->settings->name;
        foreach ($results_cursor as $row) {
            if (empty($row->collector_name)) {
                continue;
            }

            $collector_key = 'cob' . $row->user_id;

            // Extraer datos del JSON en PHP
            $document_data = json_decode($row->document_data, true);
            if (!$document_data) {
                continue;
            }
            $pago = $document_data['bbr_cliente']['bbr_tipo_pagos'][0]['bbr_pagos'][0] ?? null;

            if($pago){

                $currency = $pago['moneda'] ?? null;
                $amount = $pago['monto_pago'] ?? 0;

                if ($currency == 2) {
                    $amount_bs = floatval($amount);
                    $amount_sus = 0;
                } elseif ($currency == 1) {
                    $amount_bs = 0;
                    $amount_sus = floatval($amount);
                } else {
                    $amount_bs = floatval($amount);
                    $amount_sus = 0;
                }

                $collectors[$collector_key]['items'][] = [
                    'date' => $pago['fecha_pago'] ?? null,
                    'business' => $company_name,
                    'contract_number' => $document_data['bbr_cliente']['num_contrato'] ?? null,
                    'client_name' => $document_data['bbr_cliente']['nombre_cliente'] ?? null,
                    'quota' => $pago['num_pago'] ?? null,
                    'amount_sus' => $amount_sus,
                    'amount_bs' => $amount_bs
                ];

            }else{

                $bbr_servicio = $document_data['bbr_cliente']['bbr_servicio']['bbr_pago_servicio'];

                $currency = 1;

                $amount = $bbr_servicio['unidades_seleccionada']*$bbr_servicio['valor_unit_sus'];

                if ($currency == 2) {
                    $amount_bs = floatval($amount);
                    $amount_sus = 0;
                } elseif ($currency == 1) {
                    $amount_bs = 0;
                    $amount_sus = floatval($amount);
                } else {
                    $amount_bs = floatval($amount);
                    $amount_sus = 0;
                }

                $collectors[$collector_key]['items'][] = [
                    'date' => null,
                    'business' => $company_name,
                    'contract_number' => $document_data['bbr_cliente']['num_contrato'] ?? null,
                    'client_name' => $document_data['bbr_cliente']['nombre_cliente'] ?? null,
                    'quota' => $pago['num_pago'] ?? null,
                    'amount_sus' => $amount_sus,
                    'amount_bs' => $amount_bs
                ];


            }


            $collectors[$collector_key]['subtotal']['total_sus'] += $amount_sus;
            $collectors[$collector_key]['subtotal']['total_bs'] += $amount_bs;

            $total_sus += $amount_sus;
            $total_bs += $amount_bs;
        }

        return [
            'header' => [
                'company' => $company->settings->name,
                'nit' => $company->settings->id_number,
                'period' => $this->from_date && $this->to_date ?
                    date('d/m/Y', $this->from_date) . ' - ' . date('d/m/Y', $this->to_date) :
                    'Todos',
                'date' => Carbon::now()->format('d/m/Y H:i:s'),
                'user' => is_string($this->user) ? $this->user : (is_object($this->user) && method_exists($this->user, 'name') ? $this->user->name() : 'Usuario'),
                'branch' => 'Todas'
            ],
            'collectors' => $collectors,
            'totals' => [
                'total_sus' => $total_sus,
                'total_bs' => $total_bs
            ]
        ];
    }
}
