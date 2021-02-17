<?php

namespace EmizorIpx\ClientFel\Observers;

use EmizorIpx\ClientFel\Repository\FelProductRepository;

class FelProductObserver
{
    protected $repo;

    public function __construct(FelProductRepository $repo)
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
