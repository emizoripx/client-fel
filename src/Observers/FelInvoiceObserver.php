<?php

namespace EmizorIpx\ClientFel\Observers;


use EmizorIpx\ClientFel\Repository\FelInvoiceRequestRepository;


class FelInvoiceObserver
{
    protected $repo;

    public function __construct(FelInvoiceRequestRepository $repo)
    {
        $this->repo = $repo;
    }
    public function created($model)
    {
        info("INGRESANDO AL OBSERVER");
        if ( !is_null(request()->input('felData')) )
            $this->repo->create(request()->input('felData'), $model);

        try {
            \EmizorIpx\ClientFel\Jobs\UpdateDoctorKardexJob::dispatch($model->id);
        } catch (\Throwable $t) {
            \Log::error("Error dispatching UpdateDoctorKardexJob: " . $t->getMessage());
        }
    }

    public function updated($model)
    {
        if (!is_null(request()->input('felData')))
            $this->repo->update(request()->input('felData'), $model);

        try {
            \EmizorIpx\ClientFel\Jobs\UpdateDoctorKardexJob::dispatch($model->id);
        } catch (\Throwable $t) {
            \Log::error("Error dispatching UpdateDoctorKardexJob: " . $t->getMessage());
        }
    }

    public function deleted($model)
    {
        $this->repo->delete($model);
    }
}
