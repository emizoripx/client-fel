<?php
namespace EmizorIpx\ClientFel\Utils;

use EmizorIpx\ClientFel\Models\FelClient;
use EmizorIpx\ClientFel\Models\FelInvoice;
use EmizorIpx\ClientFel\Models\FelParametric;
use EmizorIpx\ClientFel\Models\FelSyncProduct;

class Presenter {

    

    public static function appendFelData($data, $company_id) : array
    {
        $types = TypeParametrics::getAll();

        $prametrics = array();

        foreach ( $types as $type) {
            //TODO: cached tables 
            $prametrics[][$type] = FelParametric::index($type, $company_id);
        }

        $data["fel_data"]["parametrics"] = $prametrics;
        
        $data["fel_data"]["invoices"] = FelInvoice::getByCompanyId($company_id);

        $data["fel_data"]["products"] = FelSyncProduct::getByCompanyId($company_id);

        $data["fel_data"]["clients"] = FelClient::getByCompanyId($company_id);

        return $data;

    }

    
}