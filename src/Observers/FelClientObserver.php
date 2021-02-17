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
        $this->repo->create(request()->input('fel_data'), $model);
    }

    public function updated($model)
    {
        $this->repo->create(request()->input('fel_data'), $model);
    }

    public function deleted($model)
    {
        $this->repo->delete($model);
    }
}
