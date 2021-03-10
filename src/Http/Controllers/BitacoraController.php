<?php

namespace EmizorIpx\ClientFel\Http\Controllers;

use App\Http\Controllers\BaseController;
use EmizorIpx\ClientFel\Models\BitacoraLog;
use Illuminate\Http\Request;

class BitacoraController extends BaseController
{

    public function index(Request $request)
    {
        $logs = BitacoraLog::orderBy("id","desc")->simplePaginate(30);

        return view('clientfel::bitacora', compact('logs') );
        
    }
}
