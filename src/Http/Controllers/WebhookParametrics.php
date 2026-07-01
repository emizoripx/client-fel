<?php

namespace EmizorIpx\ClientFel\Http\Controllers;

use App\Http\Controllers\BaseController;
use EmizorIpx\ClientFel\Models\FelParametric;
use EmizorIpx\ClientFel\Services\Parametrics\Parametric;
use EmizorIpx\PrepagoBags\Models\AccountPrepagoBags;
use Illuminate\Http\Request;

class WebhookParametrics extends BaseController
{

    public function updateParametrics(Request $request)
    {
        $data = $request->all();

        \EmizorIpx\ClientFel\Jobs\SyncParametricsWebhookJob::dispatch($data);

        return response()->json(['status' => 'success'], 200);
    }


}
