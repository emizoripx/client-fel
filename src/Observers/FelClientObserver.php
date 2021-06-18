<?php

namespace EmizorIpx\ClientFel\Observers;

use EmizorIpx\ClientFel\Repository\FelClientRepository;

class FelClientObserver
{

    protected $repo;

    public function __construct(FelClientRepository $repo)
    {
        $this->repo = $repo;    
    }
    public function created($model) 
    {
        if (!is_null(request()->input('felData')))
            $this->repo->create(request()->input('felData'), $model);
    }

    public function saved($model)
    {
        if (!is_null(request()->input('felData')))
            $this->repo->create(request()->input('felData'), $model);
    }

    public function deleted($model)
    {
        $this->repo->delete($model);
    }

    public function restored($model)
    {
        $this->repo->restore($model);
    }
}
