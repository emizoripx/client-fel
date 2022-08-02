<?php

namespace EmizorIpx\ClientFel\Services\OfflineEvent\Resources;

use EmizorIpx\ClientFel\Services\OfflineEvent\Resources\CompraVenta\CompraVentaResource;
use EmizorIpx\ClientFel\Utils\TypeDocumentSector;

class TypeDocumentResource {

    public static function getResourceByTypeDocument( $type_document ) {

        switch ($type_document) {
            case TypeDocumentSector::COMPRA_VENTA:
                return CompraVentaResource::class;
                break;
            
            default:
                return CompraVentaResource::class;
                break;
        }

    }

}