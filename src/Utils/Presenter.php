<?php
namespace EmizorIpx\ClientFel\Utils;

use EmizorIpx\ClientFel\Http\Resources\InvoiceResource;
use EmizorIpx\ClientFel\Http\Resources\ProductResource;
use EmizorIpx\ClientFel\Models\FelClient;
use EmizorIpx\ClientFel\Models\FelInvoiceRequest;
use EmizorIpx\ClientFel\Models\FelParametric;
use EmizorIpx\ClientFel\Models\FelSyncProduct;
use stdClass;

class Presenter {

    

    public static function appendFelData($data, $company_id) : array
    {
        $types = TypeParametrics::getAll();

        $parametrics = new stdClass;

        foreach ( $types as $type) {
            //TODO: cached tables 
            $parametrics->{$type} = FelParametric::index($type, $company_id);
        }

        $data["fel_data"]["parametrics"] = $parametrics;
        
        // $data["fel_data"]["invoices"] = InvoiceResource::collection( FelInvoiceRequest::getByCompanyId($company_id) );

        // $data["fel_data"]["products"] = ProductResource::collection(FelSyncProduct::getByCompanyId($company_id));

        // $data["fel_data"]["clients"] = FelClient::getByCompanyId($company_id);

        return $data;

    }

    
}