<?php

namespace EmizorIpx\ClientFel\Http\Controllers;

use App\Http\Controllers\BaseController;
use EmizorIpx\ClientFel\Http\Requests\GetPaymentsTerrasurRequest;
use EmizorIpx\ClientFel\Http\Requests\GetPaymentTypesTerrasurRequest;
use EmizorIpx\ClientFel\Http\Requests\SearchTerrasurRequest;
use EmizorIpx\ClientFel\Services\Terrasur\TerrasurService;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Utils\Traits\MakesHash;

class TerrasurController extends BaseController
{
    use MakesHash;
    public function indexPaymentType(GetPaymentTypesTerrasurRequest $request, $paymentType)
    {
        $service = new TerrasurService($request->all());
        $service->listPaymentTypes();   
        return $service->getResponse();
    }

    public function indexReceipts(Request $request)
    {
        $service = new TerrasurService($request->all());
        return $service->listReceipts();   
        
    }

    public function index(GetPaymentsTerrasurRequest $request, $paymentType)
    {
        $service = new TerrasurService($request->all());   
        $service->listPayments();
        return $service->getResponse();  
    }


    public function getReceiptPdf(Request $request, $id)
    {

        $service = new TerrasurService($request->all());
        return $service->getReceiptPdf ($this->decodePrimaryKey($id));   
    }

    public function search(SearchTerrasurRequest $request)
    {

        $service = new TerrasurService($request->all());
        $service->search();
        return $service->getResponse();  
    }

    public function conciliate(Request $request, $id)
    {

        $service = new TerrasurService($request->all());
        $service->conciliation($this->decodePrimaryKey($id));
        return $service->getResponse();  
    }


}