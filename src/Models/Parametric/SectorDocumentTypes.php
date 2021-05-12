<?php

namespace EmizorIpx\ClientFel\Models\Parametric;

use EmizorIpx\ClientFel\Utils\TypeInvoice;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SectorDocumentTypes extends Model
{
    protected $table = "fel_sector_document_types";

    protected $guarded = [];

    public static function getTypeInvoice($code){
        return TypeInvoice::getTypeInvoice(self::where('codigo', $code)->first()->tipoFactura);
    }
}
