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
        \Log::debug("ingresa a create fel" );
        $this->repo->create(request()->input('fel_data'), $model);
    }

    public function updated($model)
    {
        \Log::debug("ingresa a update fel con " . $model->updated_at);
        $this->repo->update(request()->input('fel_data'), $model);
    }

    public function deleted($model)
    {
        $this->repo->delete($model);
    }
}
