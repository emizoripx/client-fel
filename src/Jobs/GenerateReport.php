<?php

namespace EmizorIpx\ClientFel\Jobs;

use League\Csv\Writer;
use EmizorIpx\ClientFel\Reports\Invoices\InvoiceReport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use EmizorIpx\OfficePhp74\Drivers\PhpSpreadsheetDriver;
use EmizorIpx\OfficePhp74\Sheets\Parser;
use EmizorIpx\OfficePhp74\SheetsService;
use Carbon\Carbon;
use App\Utils\HostedPDF\NinjaPdf;
use EmizorIpx\ClientFel\Utils\ExportUtils;
use Illuminate\Support\Facades\View;
use Exception;
use SplTempFileObject;

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

    protected $type_file_pdf = false;

    protected $type_format_report = "template";

    public $timeout = 600;

    protected $invoices = null;

    protected $report_name_path = "";

    protected $file_name = "";  

    protected $headers_csv = [];                                                                                                                                                                                                                                                                                                                      

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( $request, $company_id, $company_nit, $entity, $columns, $template_path, $report_record_id, $user, $type_format_report = "template", $headers_csv=[] )
    {
        $this->request = collect($request);

        $this->entity = $entity;

        $this->company_id = $company_id;

        $this->company_nit = $company_nit;

        $this->columns = $columns;

        $this->template_path = $template_path;

        $this->report_record_id = $report_record_id;

        $this->user = $user;

        $this->type_format_report = $type_format_report;

        $this->headers_csv = $headers_csv;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        set_time_limit ( 780 );
        \Log::debug("GENERATE REPORT JOBS >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> INIT >>>>>>> 10 html 2");

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
            
            $service_report = new $report_class ($this->company_id, $this->request, $this->columns, $this->user, $this->headers_csv);

            $this->invoices = $service_report->generateReport();

            $user_settings = $this->user->token()->cu->settings;

            \Log::debug("User Settings TYpe: " . gettype($user_settings));
            \Log::debug("User Settings: " . json_encode($user_settings));

            if( isset($user_settings) && isset($user_settings->report_enable_pdf_type) && $user_settings->report_enable_pdf_type == 1 && $this->entity == ExportUtils::ITEMS_ENTITY ) {

                $this->type_file_pdf = true;

                $this->template_path = str_replace('.xlsx', '.blade.php', $this->template_path);

            }

            if( strpos($this->template_path, 'blade.php') != false ) {
                
                $this->type_file_pdf = true;

            }
    
            switch ($this->type_format_report) {

                case 'csv':
                    $this->processCsvFormat();
                    break;

                default:
                    $this->processTemplateFormat();
                    break;
            }

            \DB::table('fel_report_requests')->where('id', $this->report_record_id)->update([
                'status' => 3,
                'report_date' => Carbon::now()->toDateString(),
            ]);

            UploadReport::dispatch( $this->company_nit, $this->entity, $this->report_name_path, $this->filename, $this->report_record_id );

        
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

    public function processTemplateFormat()
    {
        $driver = new PhpSpreadsheetDriver();
        $parser = new Parser();
        $service_export = new SheetsService($driver, $parser);

        \Log::debug("Template Path: " . $this->template_path);

        $content = file_get_contents($this->template_path);

        $template_filename = ExportUtils::saveFileLocal('templateReport', Carbon::now()->toDateTimeString(), $content, $this->type_file_pdf);
        // $template_filename = storage_path('reports/daily_movement_report_template.xlsx');
        \Log::debug("File Template Path: " . $template_filename);


        if (!is_dir(storage_path('app/report'))) {
            \Log::debug("Create diretory report");
            mkdir(storage_path('app/report'));
        }

        $this->filename = "Report-$this->entity-" . hash('sha1', Carbon::now()->toDateTimeString() . md5(rand(1, 1000))) . ($this->type_file_pdf ? ".pdf" : ".xlsx");
        $this->report_name_path = storage_path("app/report/$this->filename");
        // Check pdf generate

        $init = microtime(true);
        $memory_usage = memory_get_usage();
        \Log::debug("Usage Memory: " . $memory_usage);


        if ($this->type_file_pdf) {

            $render_template = View::file($template_filename, ['data_report' => $this->invoices])->render();

            $pdf_data = (new NinjaPdf())->build($render_template, $this->report_name_path, 'reporte.pdf', true);

            file_put_contents($this->report_name_path, $pdf_data);
        } else {

            $service_export->generate($template_filename, $this->invoices)->saveAs($this->report_name_path, \EmizorIpx\OfficePhp74\Format::Xlsx);
        }

        unlink($template_filename);

        $memory_usage1 = memory_get_usage();
        \Log::debug("Usage Memory: " . $memory_usage1);
        \Log::debug(">>>>>>>>>>>>> EXECUTED-TIME generate report Invoices " . (microtime(true) - $init));

    }
    public function processCsvFormat()
    {
        $init = microtime(true);
        $memory_usage = memory_get_usage();
        \Log::debug("Usage Memory: " . $memory_usage);

        $writer = Writer::createFromFileObject(new SplTempFileObject()); //the CSV file will be created using a temporary File
        $writer->setDelimiter(","); //the delimiter will be the tab character
        $writer->setNewline("\r\n"); //use windows line endings for compatibility with some csv libraries
        $writer->setOutputBOM(Writer::BOM_UTF8);
        $writer->insertOne($this->invoices['header']);
        if ($this->entity == ExportUtils::REGISTER_SALES){

            foreach ($this->invoices['invoices']->cursor() as $record) {
                
                $writer->insertOne((array) $record);
            }
        } else if ($this->entity == ExportUtils::COMPROBANTE_DIARIO_CUSTOM1){
            $mensualidad_code = 1001;
            $matricula_code = 1000;
            $diplomado_code = 1019;
            $postgrado_code = 1018;
            $carnet_u_code = 1016;
            foreach ($this->invoices['invoices']->cursor() as $record) {
                $detail_collect = collect(json_decode($record->detalles, true));
                unset($record->detalles);
                $total_quantity_matricula = $detail_collect->where('codigoProducto', $matricula_code)->sum('subTotal');
                $total_quantity_mensualidad = $detail_collect->whereNotIn('codigoProducto', [$diplomado_code, $postgrado_code, $carnet_u_code, $matricula_code])->sum('subTotal');
                $total_quantity_otros_ingresos = $detail_collect->whereIn('codigoProducto', [$diplomado_code, $postgrado_code, $carnet_u_code])->sum('subTotal');
                $merged = array_merge((array)$record, ['matricula' => $total_quantity_matricula, 'mensualidad' => $total_quantity_mensualidad, "otros_ingresos" => $total_quantity_otros_ingresos]);
                $writer->insertOne( $merged);
            }
        
        } else {
            $writer->insertAll(json_decode(json_encode($this->invoices['invoices']), true));
        }
        $csvContent = $writer->getContent();
        $this->filename = "Report-$this->entity-" . hash('sha1', Carbon::now()->toDateTimeString() . md5(rand(1, 1000))) . ".csv";
        $this->report_name_path = storage_path("app/report/$this->filename");
        file_put_contents($this->report_name_path, $csvContent);
        $memory_usage1 = memory_get_usage();
        \Log::debug("Usage Memory: " . $memory_usage1);
        \Log::debug(">>>>>>>>>>>>> EXECUTED-TIME generate report Invoices " . (microtime(true) - $init));
    }
}
