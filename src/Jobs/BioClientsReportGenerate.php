<?php

namespace EmizorIpx\ClientFel\Jobs;

use App\Models\Company;
use App\Models\CompanyUser;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;
use EmizorIpx\ClientFel\Models\FelReportRequest;
use Exception;

class BioClientsReportGenerate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $report_id = 2;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $now = Carbon::now()->format('Y-m-d');

        $from = Carbon::parse($now)->subDays(6);

        $company = Company::where('id', 569)->first();

        $user = CompanyUser::where('company_id', $company->id)->where('is_admin', 1)->first()->user;

        $request = ['from_date' => strtotime($from), 'to_date' => strtotime($now)];

        $custom_reports = $company->settings->custom_reports;

        // \Log::debug("Cutsom Reports: " . json_encode($custom_reports));

        $report_type = null;
        if( count($custom_reports) > 1 ){
            foreach($custom_reports as $report) {
                if( $report->id == $this->report_id ) {
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
        \Log::debug("Report from: $from to $now");
        
        $report_record = FelReportRequest::create([
            "company_id" => $company->id,
            "custom_report_id" => $report_type->id,
            "entity" => $report_type->entity,
            "status" => 1,
            "registered_at" => Carbon::now()->toDateTimeString(),
            "user_id" =>$user->id,
        ]);

        GenerateReport::dispatch(
            $request, 
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
    }
}
