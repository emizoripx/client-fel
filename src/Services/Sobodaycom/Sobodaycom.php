<?php
namespace EmizorIpx\ClientFel\Services\Sobodaycom;

use App\Models\Invoice;
use EmizorIpx\ClientFel\Http\Resources\Sobodaycom\SobodaycomCategoryCustomCollectionResource;
use EmizorIpx\ClientFel\Models\FelInvoiceRequest;
use EmizorIpx\ClientFel\Utils\TemplatesUtils;
use Illuminate\Support\Facades\Blade;
class Sobodaycom {

    public $request; 
    public function __construct($request)
    {   
        $this->request = $request;
    }

    public function index()
    {
        $data = $this->request->only(['category','search']);

        if (empty($data['search']) || is_null($data['search']) ) {
            $data = \DB::table('sobodaycom_categories')
            ->where('category', $data['category'])
            ->paginate();    
        }else {
            $data = \DB::table('sobodaycom_categories')
            ->whereRaw('MATCH (description) AGAINST ("' . $data['search'] . '") and category = "' . $data['category'].'"')
                ->paginate();
        }

        return new SobodaycomCategoryCustomCollectionResource($data);
    }

    public function store() 
    {
        try {
            $data = $this->request->only(['category', 'type', 'description']);
            $obj_id = \DB::table('sobodaycom_categories')->insertGetId([
                'category' => $data['category'],
                'type' => $data['type'],
                'description' => $data['description'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $obj = \DB::table('sobodaycom_categories')->whereId($obj_id)->select('id','type','description')->first();
            return response()->json(['data' => $obj ,'success'=>true]);
        } catch (\Throwable $th) {
            bitacora_error('SOBODAYCOM-STORE:'. $data['category'],$th->getMessage());
            return response()->json([ 'success' => false, 'message'=> "Ocurrió un error al registrar"]);
        }
    }
    public function delete($id)
    {
        $data = $this->request->only(['category']);

        try {

            $obj = \DB::table('sobodaycom_categories')->whereId($id)->where('category', $data['category'])->select('id', 'type', 'description')->first();

            if (empty($obj)) {
                return response()->json(['success' => false, 'message' => 'No se encontró el registro.']);
            }

            \DB::table('sobodaycom_categories')->whereId($id)->delete();

            return response()->json(['success' => true]);
        } catch (\Throwable $th) {
            bitacora_error('SOBODAYCOM-DELETE:' . $data['category'], $th->getMessage());
            return response()->json(['success' => false, 'message' => "Ocurrió un error al registrar"]);
        }
    }

    public function update($id)
    {
        $data = $this->request->only(['category', 'type', 'description']);
        
        try {
            
            $obj = \DB::table('sobodaycom_categories')->whereId($id)->where('category',$data['category'])->select('id', 'type', 'description')->first();

            if (empty($obj)) {
                return response()->json(['success' => false, 'message' => 'No se encontró el registro.']);
            }

            \DB::table('sobodaycom_categories')->whereId($id)->update([
                'type' => $data['type'],
                'description' => $data['description'],
                'updated_at' => now(),
            ]);
            $obj = \DB::table('sobodaycom_categories')->whereId($id)->select('id', 'type', 'description')->first();
            return response()->json(['data' => $obj, 'success' => true]);
        } catch (\Throwable $th) {
            bitacora_error('SOBODAYCOM-UPDATE:' . $data['category'], $th->getMessage());
            return response()->json(['success' => false, 'message' => "Ocurrió un error al registrar"]);
        }
    }

    public function getAuthorization($id)
    {
        $felInvoiceRequest = FelInvoiceRequest::findByIdOrigin($id);

        if (!$felInvoiceRequest) {
            return response()->json([
                "success" => false,
                "msg" => "Registro inexistente"
            ]);
        }
      
        $felinvoice = $felInvoiceRequest->invoice_origin();
        $resourceClass = TemplatesUtils::getClassResourceByDocumentSector(1);
        $invoice = new $resourceClass($felinvoice);
        $data = $invoice->resolve();
        $obj = $data['sobodaycom'];
        $concatenate = function($x) use($obj) {
            return isset($obj->{$x}) ?  collect($obj->{$x})->map(function($d){ return $d->description; })->implode(",") :"";
        };

        $data['grupos_artistas'] =  $concatenate('grupos_artistas');
        $data['eventos_rubros'] =   $concatenate('eventos_rubros');
        $data['medios_transmisiones'] = $concatenate('medios_transmisiones');

        $content = file_get_contents("https://emizorv5.s3.amazonaws.com/autorizacion_template.blade.php");
        $render_template = Blade::render($content, ['fiscalDocument' => $data]);
        return $render_template;
    }
}