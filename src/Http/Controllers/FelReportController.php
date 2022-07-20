<?php

namespace EmizorIpx\ClientFel\Http\Controllers;

use App\Http\Controllers\BaseController;
use EmizorIpx\ClientFel\Models\FelReportRequest;
use Exception;
use Illuminate\Http\Request;
use Carbon\Carbon;
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

            \Log::debug("Cutsom Reports: " . json_encode($custom_reports));


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
            ]);
            
            GenerateReport::dispatch($request->all(), $company->id, $company->settings->id_number, $report_type->entity, $report_type->columns, $report_type->template, $report_record->id, $user );

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
    
}
