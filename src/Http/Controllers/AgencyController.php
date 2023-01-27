<?php

namespace EmizorIpx\ClientFel\Http\Controllers;

use App\Http\Controllers\BaseController;
use EmizorIpx\ClientFel\Http\Resources\Agencies\AgencyCustomCollectionResource;
use Illuminate\Http\Request;

class AgencyController extends BaseController
{

    public function __construct()
    {
    }

    public function index(Request $request)
    {
        $search = $request->query("filter",null);
       $data = \DB::table('agencies')
        ->when($search, function($query) use($search) {
                $query->orWhere('name','like',"%$search%")
                    ->orWhere('nit', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%");
        })->paginate();
        
        return new AgencyCustomCollectionResource($data);
    }

    public function show(Request $request, $id)
    {
        
        $agency = \DB::table('agencies')->whereId($id)->first();
        
        if (empty($agency)) {
            return response()->json([],404);
        }

        return response()->json(["data"=>$agency],200);

    }
    public function store(Request $request)
    {
        $agency = \DB::table('agencies')->insert([
            'name' => request()->name,
            'nit' => request()->nit,
            'email' => request()->email,
        ]);
        return response()->json($agency,200);
    }
    public function update(Request $request, $id)
    {
        $agency = \DB::table('agencies')
        ->whereId($id)
        ->update([
            'name' => request()->name,
            'nit' => request()->nit,
            'email' => request()->email,
        ]);
        return response()->json($agency,200);
    }

    public function verifyPolicy(Request $request)
    {
        $policy_code = $request->get('poliza',null);
        
        if (!is_null($policy_code) && \DB::table('policies_invoices')->where('policy_code', $policy_code)->exists() ) {
            return response()->json(["message"=>"La poliza ya fue registrada"],400);
        }
        return response()->json(["data" =>["message" => "La poliza esta disponible, no se registró."]], 200);
    }
}