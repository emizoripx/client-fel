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
        \Log::debug('Ingrea a created client');
        $this->repo->create(request()->input('fel_data'), $model);
    }

    public function saved($model)
    {
        \Log::debug('Ingrea a updated client');
        $this->repo->create(request()->input('fel_data'), $model);
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
