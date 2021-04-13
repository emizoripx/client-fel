<?php

namespace EmizorIpx\ClientFel\Models\Parametric;

use Illuminate\Database\Eloquent\Model;

class IdentityDocumentType extends Model
{
    protected $table = 'fel_identity_document_types';
    protected $guarded =[];

    public static function getDocumentTypeDescription( $code ){
        return self::where('codigo', $code)->first()->descripcion;
    }
}
