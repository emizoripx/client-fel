<?php

namespace EmizorIpx\ClientFel\Observers;

use EmizorIpx\ClientFel\Facades\ClientFel;
use EmizorIpx\ClientFel\Repository\FelInvoiceRepository;
use Illuminate\Support\Facades\Log;

class FelInvoiceObserver
{
    protected $repo;

    public function __construct(FelInvoiceRepository $repo)
    {
        $this->repo = $repo;
    }
    public function created($model)
    {
        $this->repo->create(request()->input('fel_data'), $model);
    }

    public function updated($model)
    {
        $this->repo->update(request()->input('fel_data'), $model);
    }

    public function deleted($model)
    {
        $this->repo->delete($model);
    }
}
