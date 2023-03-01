<?php

namespace EmizorIpx\ClientFel\Http\Controllers;

use App\Http\Controllers\BaseController;
use EmizorIpx\ClientFel\Http\Requests\StoreUpdateSobodaycomCategoriesRequest;
use EmizorIpx\ClientFel\Services\Sobodaycom\Sobodaycom;
use Illuminate\Http\Request;

class SobodaycomController extends BaseController
{

    public function index(Request $request, $category)
    {
        $service = new Sobodaycom($request);
        return $service->index();
    }

    public function store(StoreUpdateSobodaycomCategoriesRequest $request)
    {
        $service = new Sobodaycom($request);
        return $service->store();
    }
    public function update(StoreUpdateSobodaycomCategoriesRequest $request, $category, $id)
    {
        $service = new Sobodaycom($request);
        return $service->update($id);
    }
    public function delete(Request $request, $category, $id)
    {
        $service = new Sobodaycom($request);
        return $service->delete($id);
    }

    public function getAutorizacion(Request $request, $id)
    {
        $service = new Sobodaycom($request);
        return $service->getAuthorization($id);
    }
}
