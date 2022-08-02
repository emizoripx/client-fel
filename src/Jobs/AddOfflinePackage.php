<?php

namespace EmizorIpx\ClientFel\Jobs;

use EmizorIpx\ClientFel\Models\FelCufd;
use EmizorIpx\ClientFel\Models\FelCuis;
use EmizorIpx\ClientFel\Models\FelInvoiceRequest;
use EmizorIpx\ClientFel\Models\FelPOS;
use EmizorIpx\ClientFel\Models\Parametric\SectorDocumentTypes;
use EmizorIpx\ClientFel\Services\OfflineEvent\OfflineEventService;
use EmizorIpx\ClientFel\Services\OfflinePackage\OfflinePackageService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Hashids\Hashids;

class AddOfflinePackage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $fel_invoice;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( FelInvoiceRequest $fel_invoice )
    {
        $this->fel_invoice = $fel_invoice;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        \Log::debug("ADD PACKAGE OFFLINE JOBS >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> INIT");
        $offline_event_service = new OfflineEventService;
        $offline_service = new OfflinePackageService;

        \Log::debug("Data en Jobs: " . json_encode($this->fel_invoice));

        $hashid = new Hashids(config('ninja.hash_salt'), 10);
        $company_id = $hashid->decode($this->fel_invoice->company_id)[0];
        $branch = $this->fel_invoice->getBranchByCode();

        $pos_id = null;
        if( $this->fel_invoice->codigoPuntoVenta != 0 ) {
            $pos = FelPOS::where('company_id', $company_id)->where('branch_id', $branch->id)->where('codigo', $this->fel_invoice->codigoPuntoVenta )->first();

            $pos_id = empty($pos) ? $pos->id : null;
        }

        \Log::debug("Company_id: " . $company_id);
        
        \Log::debug("Branch: " . json_encode($branch));
        
        $sectorDocument = SectorDocumentTypes::where('company_id', $company_id)->where('codigo', $this->fel_invoice->type_document_sector_id)->select('codigoSistema')->first();
        \Log::debug("SectorDOcument: " . json_encode($sectorDocument));

        $cuis = FelCuis::where('company_id', $company_id)->where('branch_code', $branch->codigo)->where('system_code', $sectorDocument->codigoSistema)->where('pos_code', $this->fel_invoice->codigoPuntoVenta == null ? null : $this->fel_invoice->codigoPuntoVenta )->first();

        \Log::debug("Cuis: " . json_encode($cuis));
        $cufd = FelCufd::where('company_id', $company_id)->where('branch_code', $branch->codigo)->where('system_code', $sectorDocument->codigoSistema)->where('pos_code', $this->fel_invoice->codigoPuntoVenta == null ? null : $this->fel_invoice->codigoPuntoVenta )->first();
        \Log::debug("Cufd: " . json_encode($cufd));

        $offline_event = $offline_event_service->getOrCreateOfflineEvent($company_id, $branch->id, $pos_id, $cufd->cufd, $cuis->cuis, $this->fel_invoice->fechaEmision);

        $offline_package = $offline_service->getOrCreateOfflinePackage( $company_id, $branch->id, $pos_id, $this->fel_invoice->type_document_sector_id, $offline_event->id  );

        \Log::debug("Offline Package: " . json_encode($offline_event));
        \Log::debug("Offline Package: " . json_encode($offline_package));

        $this->fel_invoice->offline_package_id = $offline_package->id;
        $this->fel_invoice->cufd = $offline_event->cufd;
        $this->fel_invoice->cuis = $offline_event->cuis;
        $this->fel_invoice->save();

        $offline_package->quantity = $offline_package->quantity + 1;
        $offline_package->save();

        \Log::debug("ADD PACKAGE OFFLINE JOBS >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> END");
        
    }
}
