<?php

namespace EmizorIpx\ClientFel\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Exception;

class UploadReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $report_path;

    protected $report_filename;

    protected $entity;

    protected $company_nit;

    protected $report_record_id;

    public $timeout = 300;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( $company_nit, $entity, $report_path, $report_filename, $report_record_id)
    {

        $this->entity = $entity;

        $this->company_nit = $company_nit;

        $this->report_path = $report_path;

        $this->report_filename = $report_filename;

        $this->report_record_id = $report_record_id;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        
        \Log::debug("UPLOAD REPORT JOBS >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> INIT");

        try {

            

            
            $date = Carbon::now()->toDateString();
            $base_file_path = "reports/company/$this->company_nit/$this->entity/$date";
            
            \Log::debug("Base File Path: " . $base_file_path );
            
            $init = microtime(true);
            $path = Storage::disk('s3')->put($base_file_path  . "/$date-$this->report_filename",  file_get_contents($this->report_path),'public');
            \Log::debug("Path Upload: " . $path);

            \DB::table('fel_report_requests')->where('id', $this->report_record_id)->update([
                'completed_at' => Carbon::now()->toDateTimeString(),
                's3_filepath' => $base_file_path  . "/$date-$this->report_filename",
                'filename' => "$date-$this->report_filename",
            ]);

            unlink($this->report_path);

            \Log::debug(">>>>>>>>>>>>> EXECUTED-TIME generate report Invoices " . (microtime(true) - $init));
        
        } catch ( Exception $ex ) {
            \Log::debug("Error al Generar Reporte: " . $ex->getMessage());
        }
        
    }

    public function failed(Throwable $exception)
    {
        // Send user notification of failure, etc...
        \Log::debug("Ocurrio un Error en realizar la Peticion de Estado Excepción: " . $exception->getMessage());

        // Notification::route('mail', 'remberto.molina@emizor.com')->notify( new GetStatusInvoiceFailed($this->invoice, $exception->getFile() , $exception->getLine(), $exception->getMessage()) );

    }
}
