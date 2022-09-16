<?php

namespace EmizorIpx\ClientFel\Http\Controllers;

use App\Factory\InvoiceFactory;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Models\Invoice;
use App\Repositories\InvoiceRepository;
use App\Transformers\InvoiceTransformer;
use App\Utils\Traits\MakesHash;
use EmizorIpx\ClientFel\Repository\FelInvoiceRequestRepository;
use Hashids\Hashids;
use DB;
use EmizorIpx\ClientFel\Http\Requests\StoreOrderRequest;
use EmizorIpx\ClientFel\Http\Transformers\OrderTransformer;
use Exception;

class OrderController extends BaseController
{
    use MakesHash;

    protected $entity_type = Invoice::class;

    protected $entity_transformer = OrderTransformer::class;

    protected $invoice_repo;

    public function __construct( InvoiceRepository $invoice_repo )
    {
        parent::__construct();

        $this->invoice_repo = $invoice_repo;
        
    }
    
    public function store ( StoreOrderRequest $request ) {

        $company = $request->company;

        \Log::debug("Company ID: " . $company->id);

        $hashid = new Hashids(config('ninja.hash_salt'), 10);
        $user_id = $hashid->decode($request->user_id)[0];

        try {
            $inputData = FelInvoiceRequestRepository::completeOrderDataRequest($request->all(), $company, $user_id);
            $request->replace($inputData);

            \Log::debug("User id: " . $request->user_id);
            \Log::debug("User ID: " . $user_id);
            DB::beginTransaction();
            
            $invoice = $this->invoice_repo->save($request->all(), InvoiceFactory::create($company->id, $user_id));

            DB::commit();

        } catch (Exception $ex) {

            DB::rollback();
            \Log::debug("Error ". $ex->getMessage(). ' LINE '. $ex->getLine(). ' in '.$ex->getFile());
            return response()->json(['message' => $ex->getMessage()], 500);
        }

        $invoice = $invoice->service()->triggeredActions($request)->save();

        return $this->itemResponse($invoice);

    }
    
}
