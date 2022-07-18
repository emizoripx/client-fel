<?php

namespace EmizorIpx\ClientFel\Jobs;

use EmizorIpx\ClientFel\Reports\Invoices\InvoiceReport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use AnourValar\Office\Drivers\PhpSpreadsheetDriver;
use AnourValar\Office\Sheets\Parser;
use AnourValar\Office\SheetsService;
use Carbon\Carbon;
use EmizorIpx\ClientFel\Utils\ExportUtils;
use Exception;

class GenerateReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $request;

    protected $entity;

    protected $template_path;

    protected $company_id;

    protected $company_nit;

    protected $columns;

    protected $report_record_id;

    protected $user;

    public $timeout = 300;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( $request, $company_id, $company_nit, $entity, $columns, $template_path, $report_record_id, $user )
    {
        $this->request = collect($request);

        $this->entity = $entity;

        $this->company_id = $company_id;

        $this->company_nit = $company_nit;

        $this->columns = $columns;

        $this->template_path = $template_path;

        $this->report_record_id = $report_record_id;

        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        
        \Log::debug("GENERATE REPORT JOBS >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> INIT");

        try {

            \DB::table('fel_report_requests')->where('id', $this->report_record_id)->update([
                'start_process_at' => Carbon::now()->toDateTimeString(),
                'status' => 2,
                'user_id' => $this->user->id,
                'request_parameters' => json_encode([
                    'branch_code' => $this->request->has('branch_code') ? $this->request->get('branch_code') : null,
                    'type_document' => $this->request->has('type_document') ? $this->request->get('type_document') : null,
                    'state' => $this->request->has('state') ? $this->request->get('state') : null,
                    'group' => $this->request->has('group') ? $this->request->get('group') : null,
                    'from_date' => $this->request->has('from_date') ? $this->request->get('from_date') : null,
                    'to_date' => $this->request->has('to_date') ? $this->request->get('to_date') : null
                ]),
            ]);

            $report_class = ExportUtils::getClassReport($this->entity);

            \Log::debug("Report Type: "  . $report_class);
            
            $service_report = new $report_class ($this->company_id, $this->request, $this->columns, $this->user->name());

            $invoices = $service_report->generateReport();
            $driver = new PhpSpreadsheetDriver();
            $parser = new Parser();
            $service_export = new SheetsService($driver, $parser);

            \Log::debug("Template Path: " . $this->template_path );
            
            $content = file_get_contents($this->template_path);
            
            $template_filename = ExportUtils::saveFileLocal('templateReport', Carbon::now()->toDateTimeString(), $content);
            
            \Log::debug("File Template Path: " . $template_filename );

            

            if( ! is_dir(storage_path('app/report')) ) {
                \Log::debug("Create diretory report");
                mkdir(storage_path('app/report'));
            }


            $filename = "Report-$this->entity-" . hash('sha1', Carbon::now()->toDateTimeString() . md5(rand(1, 1000))).".xlsx";
            $report_name_path = storage_path("app/report/$filename");

            $init = microtime(true);
            $memory_usage = memory_get_usage();
            \Log::debug("Usage Memory: " . $memory_usage);
            $service_export->generate($template_filename, $invoices)->saveAs( $report_name_path, \AnourValar\Office\Format::Xlsx);

            unlink($template_filename);

            $memory_usage1 = memory_get_usage();
            \Log::debug("Usage Memory: " . $memory_usage1);
            \Log::debug(">>>>>>>>>>>>> EXECUTED-TIME generate report Invoices " . (microtime(true) - $init));

            \DB::table('fel_report_requests')->where('id', $this->report_record_id)->update([
                'status' => 3,
                'report_date' => Carbon::now()->toDateString(),
            ]);

            UploadReport::dispatch( $this->company_nit, $this->entity, $report_name_path, $filename, $this->report_record_id );

        
        } catch ( Exception $ex ) {
            \Log::debug("Error al Generar Reporte: " . $ex->getMessage() . " File: " . $ex->getFile() . " Line: " . $ex->getLine());
            \DB::table('fel_report_requests')->where('id', $this->report_record_id)->update([
                'status' => 4,
            ]);
        }
        
    }

    public function failed(\Throwable $exception)
    {
        // Send user notification of failure, etc...
        \Log::debug("Ocurrio un Error en realizar la Peticion de Estado Excepción: " . $exception->getMessage());
        \DB::table('fel_report_requests')->where('id', $this->report_record_id)->update([
            'status' => 4,
        ]);
        // Notification::route('mail', 'remberto.molina@emizor.com')->notify( new GetStatusInvoiceFailed($this->invoice, $exception->getFile() , $exception->getLine(), $exception->getMessage()) );

    }
}
