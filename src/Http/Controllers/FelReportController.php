<?php

namespace EmizorIpx\ClientFel\Http\Controllers;

use App\Http\Controllers\BaseController;
use EmizorIpx\ClientFel\Models\FelReportRequest;
use Exception;
use Illuminate\Http\Request;
use Carbon\Carbon;
use EmizorIpx\ClientFel\Http\Resources\ReportResource;
use EmizorIpx\ClientFel\Jobs\GenerateReport;

class FelReportController extends BaseController
{

    public function getGenerateReport( Request $request ) {

        try {

            \Log::debug("GENERATE-INOICE: INIT ");

            $user = auth()->user();
            $company = $user->company();

            $report_id = $request->get('report_id');

            \Log::debug("GENERATE-INOICE: Company ID " . $company->id);
            \Log::debug("GENERATE-INOICE: Report ID " . $report_id);
    
            if( !isset($company->settings->custom_reports) ) {
                throw new Exception('No se tiene configurado este tipo de reporte');
            }

            $custom_reports = $company->settings->custom_reports;

            // \Log::debug("Cutsom Reports: " . json_encode($custom_reports));

            $report_type = null;
            if( count($custom_reports) > 1 ){
                foreach($custom_reports as $report) {
                    if( $report->id == $report_id ) {
                        $report_type = $report;
                        break;
                    }
                }
            } else {
                $report_type = $custom_reports[0];

            }


            \Log::debug("Report Type: " . json_encode($report_type));

            if( empty($report_type) || is_null($report_type) ) {

                throw new Exception('No se tiene configurado este tipo de reporte');
            }


            if ( FelReportRequest::existReportsInProcess($user->id, $report_type->id)) {
                
                throw new Exception('Existe un reporte en proceso, espere por favor.');

            }

            $report_record = FelReportRequest::create([
                "company_id" => $company->id,
                "custom_report_id" => $report_type->id,
                "entity" => $report_type->entity,
                "status" => 1,
                "registered_at" => Carbon::now()->toDateTimeString(),
                "user_id" =>$user->id,
            ]);
            $request_array = $request->all();
            $request_array['revocated_zero'] = isset($report_type->revocated_zero) ? $report_type->revocated_zero : false;
            $request->replace($request_array);

            GenerateReport::dispatch(
                $request->all(), 
                $company->id, 
                $company->settings->id_number, 
                $report_type->entity, 
                $report_type->columns, 
                $report_type->template, 
                $report_record->id, 
                $user, 
                isset($report_type->type_format_report)? $report_type->type_format_report:"template",
                isset($report_type->headers_csv)? $report_type->headers_csv:[],
             );

            return response()->json([
                "success" => true,
                "message" => 'Generación de Reporte en Proceso',
            ]);

        } catch (Exception $ex) {

            \Log::debug("Error to generate custom reports: " . $ex->getMessage() . ' File: ' . $ex->getFile() . ' Line: ' . $ex->getLine());
            return response()->json([
                "success" => false,
                "message" => $ex->getMessage()
            ]);

        }

    }

    public function index ( Request $request ) {

        try {
            $user = auth()->user();

            $company = $user->company();

            $reports = \DB::table('fel_report_requests')->join('users', 'fel_report_requests.user_id', '=', 'users.id')
                        ->where('fel_report_requests.company_id', $company->id)
                        ->where( function( $query ) use ($user) {
                            if( ! $user->isAdmin() ) {
                                return $query->where('user_id', $user->id);
                            }

                            return $query;
                        })
                        ->select('fel_report_requests.company_id',\DB::raw('JSON_EXTRACT(request_parameters, "$.from_date") as from_date'), \DB::raw('JSON_EXTRACT(request_parameters, "$.to_date") as to_date'), 'fel_report_requests.entity', 'fel_report_requests.status', 'fel_report_requests.s3_filepath', 'fel_report_requests.report_date', 'fel_report_requests.registered_at', 'fel_report_requests.start_process_at', 'fel_report_requests.completed_at', 'users.first_name', 'users.last_name', 'fel_report_requests.user_id')
                        ->orderBy('fel_report_requests.created_at', 'DESC')
                        ->paginate(30);

            return response()->json([
                'success' => true,
                'data' => ReportResource::collection($reports)
            ]);
        } catch( Exception $ex ) {

            \Log::debug("Ocurrio un error al obtener los reportes: " . $ex->getMessage() . ' File: ' . $ex->getFile() . ' Line: ' . $ex->getLine());

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener reportes: ' . $ex->getMessage()
            ]);
        }

    }

    public function getGraphicReports(Request $request, $period)
    {
        $branch_code = $request->get('branch_code',null);
        
        if (!preg_match('/^[0-9]+$/', $branch_code)) {
            $branch_code = null;
        }else {
            $branch_code = intval($branch_code);
        } 
        
        switch ($period) {
            case 'mensual':
                return $this->graphicReport(1, $branch_code);
            case 'bimestral':
                return $this->graphicReport(2, $branch_code);
            case 'trimestral':
                return $this->graphicReport(3, $branch_code);
            case 'semestral':
                return $this->graphicReport(6, $branch_code);
            case 'anual':
                return $this->graphicReport(12, $branch_code);
            default:
                return [];
                break;
        }
    }


    private function getLastXMonths($month_number)
    {
        $getMonth = function ($month_number) {
            $months = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
            return $months[intval($month_number) - 1];
        };

        $today = Carbon::now()->timezone("America/La_Paz");
        $year = $today->format("Y");
        $lastMonths = [];

        for ($i = 0; $i < $month_number; $i++) {
            if ($year != $today->format("Y"))
                break;

            $lastMonths[] = '"' . $today->format('Y-m') . '"';
            $months_available[$today->format('Y-m')] = $getMonth($today->format('m')) . " " . $year;
            $today->subMonth();
        }
        $dates = '(' . implode(', ', array_reverse($lastMonths)) . ')';
        return [$dates, array_reverse($months_available)];
    }


    public function graphicReport($number_months, $branch_code = null)
    {
        $company = auth()->user()->company();
        $timestamps = "GET_GRAPHIC_REPORT => ". $company->settings->name . " >> MONTHS = " . $number_months." >>";
        info($timestamps . "usuario > " . json_encode(auth()->user()->name()));
       
        $x_last_months = $this->getLastXMonths($number_months);
        $dates = $x_last_months[0];
        $months_available = $x_last_months[1];
        $branches = "";
        info($timestamps . "fechas obtenidas => " . $dates );
        if (!auth()->user()->isAdmin() && !auth()->user()->isOwner()) {
            info($timestamps . "El usuario no es administardor");
            $access_branches = auth()->user()->getOnlyBranchAccess();
            if (in_array(0, $access_branches)) {
                array_push($access_branches, 1);
            }
            $formattedNumbers = [];

            foreach ($access_branches as $number) {
                $formattedNumbers[] = '"' . $number . '"';
            }

            if (!is_null($branch_code) && in_array($branch_code, $access_branches)) {
                $branches = ' and codigoSucursal = "' . $branch_code . '"';
            } else {
                $branches = '(' . implode(', ', $formattedNumbers) . ')';
                info($timestamps . " sucursal -> " . $branches);
                $branches = ' and codigoSucursal in ' . $branches;
            }

        } else {
            if (!is_null($branch_code)) {
                $branches = ' and codigoSucursal = "' . $branch_code . '"';
            }
        }

        $result = $this->getQuery($company->id, $dates, $branches);
        return $this->transform($result, $months_available);

    }

    private function getQuery($company_id, $dates, $branches = "" )
    {
        return \DB::select(\DB::raw('
            SELECT yearmonth as mes , round(SUM(balance),2) AS total_debts, round(SUM(amount-balance),2) AS total_payment
              FROM invoices
                where company_id = ' . $company_id .'
                and exists (
                    select 1 from fel_invoice_requests 
                    where fel_invoice_requests.id_origin = invoices.id
                    and company_id = ' . $company_id . '
                    and exists (
                    	select 1 from fel_invoice_requests 
                    	where fel_invoice_requests.id_origin = invoices.id 
                    	 and exists (
	                    	select 1 from fel_invoice_requests 
	                    	where fel_invoice_requests.id_origin = invoices.id 
	                    	' . $branches . '
                    	)
                    	and company_id = ' . $company_id .'	
                    	and (fel_invoice_requests.codigoEstado is null or fel_invoice_requests.codigoEstado not in ( 691, 902) )
                    )
                    and fel_invoice_requests.estado is not null
                ) 
                and yearmonth in ' . $dates . '
                GROUP BY yearmonth;
            '));
    }

    private function transform($report_result, $months_available)
    {
        $formattedResult = [];

        foreach($months_available as $code => $m) {
            $formattedResult[$code] = [
                "mes" => $m,
                "total_debts" => "0.00",
                "total_payment" => "0.00",
                "total" => "0.00",
            ];
        }
        
        foreach ($formattedResult as $key => $value) {
            foreach ($report_result as $rr) {
                if ($key == $rr->mes) {
                    $formattedResult[$key]["total_debts"] = $rr->total_debts;
                    $formattedResult[$key]["total_payment"] = $rr->total_payment;
                    $formattedResult[$key]["total"] = round(($rr->total_payment+ $rr->total_debts),2);
                }
            }
        }
        return [
            "data" => array_values($formattedResult),
            "total_debts" => collect($formattedResult)->sum('total_debts'),
            "total_payment" => collect($formattedResult)->sum('total_payment'),
            "total" => collect($formattedResult)->sum('total'),
        ];
    }
    
}
