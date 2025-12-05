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
        $query_invoices = \DB::table('invoices')
            ->join('fel_invoice_requests', 'fel_invoice_requests.id_origin', '=', 'invoices.id')
            ->leftJoin('users', 'users.id', '=', 'invoices.user_id')
            ->where('fel_invoice_requests.codigoEstado', 690)
            ->where('fel_invoice_requests.company_id', $this->company_id)
            ->whereNotNull('invoices.document_data');

        $query_invoices = $this->addDateFilter($query_invoices);
        $query_invoices = $this->addBranchFilter($query_invoices);

        $query_invoices->select(
            'users.first_name as collector_name',
            'users.id as user_id',
            'fel_invoice_requests.fechaEmision',
            'fel_invoice_requests.numeroFactura'
        )
            ->selectRaw('JSON_UNQUOTE(JSON_EXTRACT(invoices.document_data, "$.bbr_cliente.bbr_tipo_pagos[0].bbr_pagos[0].fecha_pago")) as payment_date')
            ->selectRaw('JSON_UNQUOTE(JSON_EXTRACT(invoices.document_data, "$.bbr_cliente.bbr_tipo_pagos[0].bbr_pagos[0].num_pago")) as quota')
            ->selectRaw('JSON_EXTRACT(invoices.document_data, "$.bbr_cliente.bbr_tipo_pagos[0].bbr_pagos[0].monto_pago") as amount')
            ->selectRaw('JSON_EXTRACT(invoices.document_data, "$.bbr_cliente.bbr_tipo_pagos[0].bbr_pagos[0].moneda") as currency')
            ->selectRaw('JSON_UNQUOTE(JSON_EXTRACT(invoices.document_data, "$.bbr_cliente.bbr_tipo_pagos[0].bbr_pagos[0].num_contrato")) as contract_number')
            ->selectRaw('JSON_UNQUOTE(JSON_EXTRACT(invoices.document_data, "$.bbr_cliente.nombre_cliente")) as client_name')
            ->selectRaw('JSON_UNQUOTE(JSON_EXTRACT(invoices.document_data, "$.bbr_cliente.bbr_contrato.unidad_negocio")) as business');

        $results = $query_invoices->get();

        $company = Company::query()->where('id', $this->company_id)->first();

        \Log::debug("Report Data: " . json_encode($this->formatReportData($results, $company->users)));

        return $this->formatReportData($results, $company->users);
    }

    private function formatReportData($results, $all_users)
    {
        $collectors = [];
        $total_sus = 0;
        $total_bs = 0;

        // Inicializar collectors para todos los usuarios
        foreach ($all_users as $user) {
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

        foreach ($results as $row) {
            if (empty($row->collector_name)) {
                continue;
            }

            $collector_key = 'cob' . $row->user_id;

            if (!isset($collectors[$collector_key])) {
                $collectors[$collector_key] = [
                    'name' => $row->collector_name,
                    'items' => [],
                    'subtotal' => [
                        'total_sus' => 0,
                        'total_bs' => 0
                    ]
                ];
            }

            if ($row->currency == 1) {
                $amount_bs = floatval($row->amount);
                $amount_sus = 0;
            } elseif ($row->currency == 2) {
                $amount_bs = 0;
                $amount_sus = floatval($row->amount);
            } else {
                $amount_bs = floatval($row->amount);
                $amount_sus = 0;
            }

            $collectors[$collector_key]['items'][] = [
                'date' => $row->payment_date,
                'business' => $row->business ?? 'BBR S.A.',
                'contract_number' => $row->contract_number,
                'client_name' => $row->client_name,
                'quota' => $row->quota,
                'amount_sus' => $amount_sus,
                'amount_bs' => $amount_bs
            ];

            $collectors[$collector_key]['subtotal']['total_sus'] += $amount_sus;
            $collectors[$collector_key]['subtotal']['total_bs'] += $amount_bs;

            $total_sus += $amount_sus;
            $total_bs += $amount_bs;
        }

        return [
            'header' => [
                'company' => 'RENACER S.R.L.',
                'nit' => 'NIT_PENDIENTE',
                'period' => $this->from_date && $this->to_date ?
                    date('d/m/Y', $this->from_date) . ' - ' . date('d/m/Y', $this->to_date) :
                    'Todos',
                'date' => Carbon::now()->format('d/m/Y H:i:s'),
                'user' => is_string($this->user) ? $this->user : (is_object($this->user) && method_exists($this->user, 'name') ? $this->user->name() : 'Usuario'),
                'branch' => $this->branch_code ? 'Sucursal ' . $this->branch_code : 'Todas'
            ],
            'collectors' => $collectors,
            'totals' => [
                'total_sus' => $total_sus,
                'total_bs' => $total_bs
            ]
        ];
    }
}
