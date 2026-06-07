<?php

namespace EmizorIpx\ClientFel\Models;

use EmizorIpx\ClientFel\Exceptions\ClientFelException;
use EmizorIpx\ClientFel\Models\Parametric\Country;
use EmizorIpx\ClientFel\Models\Parametric\Currency;
use EmizorIpx\ClientFel\Models\Parametric\IdentityDocumentType;
use EmizorIpx\ClientFel\Models\Parametric\PaymentMethod;
use EmizorIpx\ClientFel\Models\Parametric\SINProduct;
use EmizorIpx\ClientFel\Models\Parametric\RevocationReason;
use EmizorIpx\ClientFel\Models\Parametric\SectorDocumentTypes;
use EmizorIpx\ClientFel\Models\Parametric\Unit;
use EmizorIpx\ClientFel\Utils\TypeParametrics;
use Carbon\Carbon;
use DateTime;
use EmizorIpx\ClientFel\Models\Parametric\FelRoomType;

class FelParametric
{



    public static function create($type, $data, $company_id = null)
    {
        switch ($type) {
            case TypeParametrics::ACTIVIDADES:
                $data_ = array();
                foreach($data as $d) {
                    $d["company_id"] = $company_id;
                    $d["codigo"] = $d['codigo'];
                    $d["descripcion"] = $d['descripcion'];
                    $d["tipoActividad"] = $d['tipoActividad'];
                    $data_[] = $d;
                }
                return FelActivity::insert(static::addTimeStamp($data_));
                break;
            case TypeParametrics::LEYENDAS:
                $data_ = array();
                foreach($data as $d) {
                    $d["company_id"] = $company_id;
                    $d["codigo"] = $d['codigo'];
                    $d["codigoActividad"] = $d['codigoActividad'];
                    $d["descripcion"] = $d['descripcion'];
                    $data_[] = $d;
                }
                
                return FelCaption::insert(static::addTimeStamp($data_));
                break;
            case TypeParametrics::MONEDAS:
                return Currency::insert(static::addTimeStamp($data));
                break;

            case TypeParametrics::METODOS_DE_PAGO:
                return PaymentMethod::insert(static::addTimeStamp($data));
                break;
            case TypeParametrics::PAISES:
                return Country::insert(static::addTimeStamp($data));
                break;
            case TypeParametrics::TIPOS_DOCUMENTO_IDENTIDAD:
                return IdentityDocumentType::insert(static::addTimeStamp($data));
                break;
            case TypeParametrics::MOTIVO_ANULACION:
                return RevocationReason::insert(static::addTimeStamp($data));
                break;
            case TypeParametrics::UNIDADES:
                return Unit::insert(static::addTimeStamp($data));
                break;
            case TypeParametrics::PRODUCTOS_SIN:

                $data_ = array();
                foreach($data as $d) { 
                    $d["company_id"] = $company_id;
                    $d["codigo"] = $d['codigo'];
                    $d["codigoActividad"] = $d['codigoActividad'];
                    $d["descripcion"] = $d['descripcion'];
                    $data_[] = $d;
                }
                return SINProduct::insert(static::addTimeStamp($data_));
                break;
            case TypeParametrics::TIPOS_DOCUMENTO_SECTOR:

                $data_ = array();
                foreach($data as $d) { 
                    $d["company_id"] = $company_id;
                    $d["codigoSucursal"] = $d['codigoSucursal'];
                    $d["codigo"] = $d['codigoDocumentSector'];
                    $d["documentoSector"] = $d['documentoSector'];
                    $d["tipoFactura"] = $d['tipoFactura'];

                    unset($d['codigoDocumentSector']);
                    $data_[] = $d;
                }
                return SectorDocumentTypes::insert(static::addTimeStamp($data_));
                break;
            case TypeParametrics::ACTIVIDADES_DOCUMENTO_SECTOR:

                $data_ = array();
                foreach($data as $d) { 
                    $d["company_id"] = $company_id;
                    $d["codigoActividad"] = $d['codigoActividad'];
                    $d["actividad"] = $d['actividad'];
                    $d["codigoDocumentoSector"] = $d['codigo'];
                    $d["tipoDocumentoSector"] = $d['tipoDocumentoSector'];
                    $d["documentoSector"] = $d['descripcion'];

                    unset($d['codigo']);
                    unset($d['descripcion']);
                    $data_[] = $d;
                }
                return FelActivityDocumentSector::insert(static::addTimeStamp($data_));
                break;
            case TypeParametrics::TIPOS_HABITACION :
                return FelRoomType::insert(static::addTimeStamp($data));
                break;
            default:
                throw new ClientFelException("No existe el tipo este metodo");
                break;
        }
    }

    public static function addTimeStamp($data)
    {
        $data_added = [];

        foreach($data as $d) {
            if(isset($d['isActive'])){
                unset($d['isActive']);
            }
            $d["updated_at"] = Carbon::now()->toDateTimeString();
            $d["created_at"] = Carbon::now()->toDateTimeString();
            $data_added[] = $d;
        }
        return $data_added;
    }

    public static function index($type, $company_id)
    {
        switch ($type) {
            case TypeParametrics::ACTIVIDADES:
                $query = FelActivity::whereCompanyId($company_id)->orderBy('descripcion');
                break;
            case TypeParametrics::LEYENDAS:
                $query = FelCaption::whereCompanyId($company_id)->whereDisabled(0)->orderBy('descripcion');
                break;
            case TypeParametrics::MONEDAS:
                if ( $company_id == 568 ) { // COTEOR IN PRODUCTION
                    $query = Currency::whereIn('codigo',[1,2])->orderBy('descripcion');
                }else {
                    $query = Currency::orderBy('descripcion');
                }
                break;
            case TypeParametrics::METODOS_DE_PAGO:
                if ($company_id == 568 || $company_id == 288) { // COTEOR, sobodaycom IN PRODUCTION
                    $query = PaymentMethod::whereIn('codigo', [1, 3, 7, 8])->orderBy('descripcion');
                } else {
                    $query = PaymentMethod::orderBy('descripcion');
                }
                break;
            case TypeParametrics::PAISES:
                $query = Country::orderBy('descripcion');
                break;
            case TypeParametrics::TIPOS_DOCUMENTO_IDENTIDAD:
                $query = IdentityDocumentType::orderBy('descripcion');
                break;
            case TypeParametrics::MOTIVO_ANULACION:
                $query = RevocationReason::orderBy('descripcion');
                break;
            case TypeParametrics::UNIDADES:
                $query = Unit::orderBy('descripcion');
                break;
            case TypeParametrics::PRODUCTOS_SIN:
                $query = SINProduct::whereCompanyId($company_id)->orderBy('descripcion');
                break;
            case TypeParametrics::TIPOS_DOCUMENTO_SECTOR:
                $query = SectorDocumentTypes::whereCompanyId($company_id);
                break;
            case TypeParametrics::ACTIVIDADES_DOCUMENTO_SECTOR:
                $query = FelActivityDocumentSector::whereCompanyId($company_id)->orderBy('actividad');
                break;
            case TypeParametrics::TIPOS_HABITACION:
                $query = FelRoomType::orderBy('descripcion');
                break;
            default:
                throw new ClientFelException("No existe el tipo este metodo");
                break;
        }

        // TODO: add logic in frontend to cached parametrics and then uncomment
        // if (request()->has('updated_at') && request()->input('updated_at') > 0) {
        //     $query->where('updated_at', '>=', date('Y-m-d H:i:s', intval(request()->input('updated_at'))));
        // }

        return $query->get();
    }

    public static function existsParametric($type, $company_id){
        switch ($type) {
            case TypeParametrics::ACTIVIDADES:
                $activity = FelActivity::where('company_id',$company_id)->first();
                return is_null($activity);
                break;
            
            case TypeParametrics::LEYENDAS:
                $caption = FelCaption::whereCompanyId($company_id)->first();
                return is_null($caption);
                break;
            
            case TypeParametrics::MONEDAS:
                // TODO: find another way to do it
                
                $currencies = Currency::first();
                return is_null($currencies);

                break;
            
            case TypeParametrics::METODOS_DE_PAGO:
                $payment = PaymentMethod::first();
                return is_null($payment);
                break;
            
            case TypeParametrics::PAISES:
                $country  = Country::first();
                return is_null($country);
                break;
            
            case TypeParametrics::TIPOS_DOCUMENTO_IDENTIDAD:
                $identityDocument = IdentityDocumentType::first();
                return is_null($identityDocument);
                break;
            
            case TypeParametrics::MOTIVO_ANULACION:
                $revocationReason = RevocationReason::first();
                return is_null($revocationReason);
                break;
            
            case TypeParametrics::UNIDADES:
                $units = Unit::first();
                return is_null($units);
                break;
            case TypeParametrics::PRODUCTOS_SIN:
                $products = SINProduct::whereCompanyId($company_id)->first();
                return is_null($products);
                break;
            case TypeParametrics::TIPOS_DOCUMENTO_SECTOR:
                $sector_documents = SectorDocumentTypes::whereCompanyId($company_id)->first();
                return is_null($sector_documents);
                break;
            case TypeParametrics::ACTIVIDADES_DOCUMENTO_SECTOR:
                $activity_sector_documents = FelActivityDocumentSector::whereCompanyId($company_id)->first();
                return is_null($activity_sector_documents);
                break;
            case TypeParametrics::TIPOS_HABITACION:
                $room_type = FelRoomType::first();
                return is_null($room_type);
                break;
            
            default:
                throw new ClientFelException("No existe el tipo de parametrica");
                break;
        }
    }

    public static function createOrUpdate($type, $data, $company_id = null){
        $unlinked = 0;
        $action = "updated";

        switch ($type) {
            case TypeParametrics::ACTIVIDADES:

                    $felActivity = FelActivity::where('company_id', $company_id)->where('codigo', $data['codigo'])->first();

                    if(is_null($felActivity)){

                        unset($data['isActive']);
                        static::create($type, [$data], $company_id);
                        $action = "created";
                    }
                    elseif ( !is_null($felActivity) && $data['isActive'] == false) {
                        $felActivity->delete();
                        $action = "deleted";
                        $unlinked = \EmizorIpx\ClientFel\Models\FelSyncProduct::where('company_id', $company_id)->where('codigo_actividad_economica', $data['codigo'])->update(['codigo_actividad_economica' => null]);
                    } 
                    else{
                        $felActivity->update([
                            'codigo' => $data['codigo'],
                            'descripcion' => $data['descripcion'],
                            'tipoActividad' => $data['tipoActividad'],
                            'updated_at' => Carbon::now()->toDateTimeString()
                        ]);
                    }
                break;
            case TypeParametrics::LEYENDAS:
                    $felCaption = FelCaption::where('company_id', $company_id)->where('codigo', $data['codigo'])->where('codigoActividad', $data['codigoActividad'])->first();

                    if(is_null($felCaption)){
                        unset($data['isActive']);
                        static::create($type, [$data], $company_id);
                        $action = "created";
                    }
                    elseif (!is_null($felCaption) && $data['isActive'] == false) {
                        $felCaption->delete();
                        $action = "deleted";
                    }
                    else{
                        $felCaption->update([
                            'codigo' => $data['codigo'],
                            'descripcion' => $data['descripcion'],
                            'codigoActividad' => $data['codigoActividad'],
                            'updated_at' => Carbon::now()->toDateTimeString()
                        ]);
                    }
                break;
            case TypeParametrics::PRODUCTOS_SIN:

                $sinProduct = SINProduct::where('company_id', $company_id)->where('codigo', $data['codigo'])->where('codigoActividad', $data['codigoActividad'])->first();

                if(is_null($sinProduct)){
                    unset($data['isActive']);
                    static::create($type, [$data], $company_id);
                        $action = "created";
                }
                elseif (!is_null($sinProduct) && $data['isActive'] == false) {
                    $sinProduct->delete();
                        $action = "deleted";
                    $unlinked = \EmizorIpx\ClientFel\Models\FelSyncProduct::where('company_id', $company_id)->where('codigo_producto_sin', $data['codigo'])->update(['codigo_producto_sin' => null]);
                }
                else {
                    $sinProduct->update([
                        'codigo' => $data['codigo'],
                        'descripcion' => $data['descripcion'],
                        'codigoActividad' => $data['codigoActividad'],
                        'updated_at' => Carbon::now()->toDateTimeString()
                    ]);
                }

                break;
            case TypeParametrics::ACTIVIDADES_DOCUMENTO_SECTOR:

                $felActivityDocumentSector = FelActivityDocumentSector::where('company_id', $company_id)->where('codigoDocumentoSector', $data['codigo'])->where('codigoActividad', $data['codigoActividad'])->first();

                if(is_null($felActivityDocumentSector)){
                    unset($data['isActive']);
                    static::create($type, [$data], $company_id);
                        $action = "created";
                }
                elseif (!is_null($felActivityDocumentSector) && $data['isActive'] == false) {
                    $felActivityDocumentSector->delete();
                        $action = "deleted";
                }
                else {
                    $felActivityDocumentSector->update([
                        'codigoDocumentoSector' => $data['codigo'],
                        'actividad' => $data['actividad'],
                        'codigoActividad' => $data['codigoActividad'],
                        'documentoSector' => $data['descripcion'],
                        'tipoDocumentoSector' => $data['tipoDocumentoSector'],
                        'updated_at' => Carbon::now()->toDateTimeString()
                    ]);
                }

                break;
            case TypeParametrics::TIPOS_DOCUMENTO_SECTOR:

                $felSectorDocument = SectorDocumentTypes::where('company_id', $company_id)->where('codigo', $data['codigoDocumentSector'])->where('codigoSucursal', $data['codigoSucursal'])->first();

                if(is_null($felSectorDocument)){
                    unset($data['isActive']);
                    static::create($type, [$data], $company_id);
                        $action = "created";
                }
                elseif (!is_null($felSectorDocument) && $data['isActive'] == false) {
                    $felSectorDocument->delete();
                        $action = "deleted";
                }
                else {
                    $felSectorDocument->update([
                        'codigoSucursal' => $data['codigoSucursal'],
                        'codigo' => $data['codigoDocumentSector'],
                        'documentoSector' => $data['documentoSector'],
                        'tipoFactura' => $data['tipoFactura'],
                        'updated_at' => Carbon::now()->toDateTimeString()
                    ]);
                }

                break;
            case TypeParametrics::MOTIVO_ANULACION:

                $motivoAnulacion = RevocationReason::where('codigo', $data['codigo'])->first();

                if(is_null($motivoAnulacion)){
                    unset($data['isActive']);
                    static::create($type, [$data], $company_id);
                        $action = "created";
                }
                elseif (!is_null($motivoAnulacion) && $data['isActive'] == false) {
                    $motivoAnulacion->delete();
                        $action = "deleted";
                }
                else {
                    $motivoAnulacion->update([
                        'codigo' => $data['codigo'],
                        'descripcion' => $data['descripcion'],
                        'updated_at' => Carbon::now()->toDateTimeString()
                    ]);
                }

                break;
            case TypeParametrics::PAISES:

                $pais = Country::where('codigo', $data['codigo'])->first();

                if(is_null($pais)){
                    unset($data['isActive']);
                    static::create($type, [$data], $company_id);
                        $action = "created";
                }
                elseif (!is_null($pais) && $data['isActive'] == false) {
                    $pais->delete();
                        $action = "deleted";
                }
                else {
                    $pais->update([
                        'codigo' => $data['codigo'],
                        'descripcion' => $data['descripcion'],
                        'updated_at' => Carbon::now()->toDateTimeString()
                    ]);
                }

                break;
            case TypeParametrics::TIPOS_DOCUMENTO_IDENTIDAD:

                $documentIdentidad = IdentityDocumentType::where('codigo', $data['codigo'])->first();

                if(is_null($documentIdentidad)){
                    unset($data['isActive']);
                    static::create($type, [$data], $company_id);
                        $action = "created";
                }
                elseif (!is_null($documentIdentidad) && $data['isActive'] == false) {
                    $documentIdentidad->delete();
                        $action = "deleted";
                }
                else {
                    $documentIdentidad->update([
                        'codigo' => $data['codigo'],
                        'descripcion' => $data['descripcion'],
                        'updated_at' => Carbon::now()->toDateTimeString()
                    ]);
                }

                break;
            case TypeParametrics::METODOS_DE_PAGO:

                $metodosPago = PaymentMethod::where('codigo', $data['codigo'])->first();

                if(is_null($metodosPago)){
                    unset($data['isActive']);
                    static::create($type, [$data], $company_id);
                        $action = "created";
                }
                elseif (!is_null($metodosPago) && $data['isActive'] == false) {
                    $metodosPago->delete();
                        $action = "deleted";
                }
                else {
                    $metodosPago->update([
                        'codigo' => $data['codigo'],
                        'descripcion' => $data['descripcion'],
                        'updated_at' => Carbon::now()->toDateTimeString()
                    ]);
                }

                break;
            case TypeParametrics::MONEDAS:

                $currency = Currency::where('codigo', $data['codigo'])->first();

                if(is_null($currency)){
                    unset($data['isActive']);
                    static::create($type, [$data], $company_id);
                        $action = "created";
                }
                elseif (!is_null($currency) && $data['isActive'] == false) {
                    $currency->delete();
                        $action = "deleted";
                }
                else {
                    $currency->update([
                        'codigo' => $data['codigo'],
                        'descripcion' => $data['descripcion'],
                        'updated_at' => Carbon::now()->toDateTimeString()
                    ]);
                }

                break;
            case TypeParametrics::UNIDADES:

                $unit = Unit::where('codigo', $data['codigo'])->first();

                if(is_null($unit)){
                    unset($data['isActive']);
                    static::create($type, [$data], $company_id);
                        $action = "created";
                }
                elseif (!is_null($unit) && $data['isActive'] == false) {
                    $unit->delete();
                        $action = "deleted";
                    $unlinked = \EmizorIpx\ClientFel\Models\FelSyncProduct::where('company_id', $company_id)->where('codigo_unidad', $data['codigo'])->update(['codigo_unidad' => null, 'nombre_unidad' => null]);
                }
                else {
                    $unit->update([
                        'codigo' => $data['codigo'],
                        'descripcion' => $data['descripcion'],
                        'updated_at' => Carbon::now()->toDateTimeString()
                    ]);
                }

                break;
            case TypeParametrics::TIPOS_HABITACION:

                $room_type = FelRoomType::where('codigo', $data['codigo'])->first();

                if(is_null($room_type)){
                    unset($data['isActive']);
                    static::create($type, [$data], $company_id);
                        $action = "created";
                }
                elseif (!is_null($room_type) && $data['isActive'] == false) {
                    $room_type->delete();
                        $action = "deleted";
                }
                else {
                    $room_type->update([
                        'codigo' => $data['codigo'],
                        'descripcion' => $data['descripcion'],
                        'updated_at' => Carbon::now()->toDateTimeString()
                    ]);
                }

                break;
            
            default:
                throw new ClientFelException("No existe el tipo este metodo");
                break;
        }

        return ["action" => $action, "unlinked" => $unlinked];
    }

    public static function getUpdatedAt($type, $company_id){
        switch ($type) {
            case TypeParametrics::ACTIVIDADES:
                $updated_at = FelActivity::where('company_id', $company_id)->orderByDesc('updated_at')->pluck('updated_at')->first();
                return strtotime($updated_at);
                
                break;
            case TypeParametrics::LEYENDAS:
                $updated_at = FelCaption::where('company_id', $company_id)->orderByDesc('updated_at')->pluck('updated_at')->first();
                return strtotime( $updated_at );

                break;
            case TypeParametrics::PRODUCTOS_SIN:
                $updated_at = SINProduct::where('company_id', $company_id)->orderByDesc('updated_at')->pluck('updated_at')->first();
                return strtotime($updated_at);

                break;
            case TypeParametrics::ACTIVIDADES_DOCUMENTO_SECTOR:
                $updated_at = FelActivityDocumentSector::where('company_id', $company_id)->orderByDesc('updated_at')->pluck('updated_at')->first();
                return strtotime( strval( $updated_at));

                break;
            case TypeParametrics::TIPOS_DOCUMENTO_SECTOR:
                $updated_at = SectorDocumentTypes::where('company_id', $company_id)->orderByDesc('updated_at')->pluck('updated_at')->first();
                return strtotime( strval( $updated_at));

                break;
            case TypeParametrics::MOTIVO_ANULACION:
                $updated_at = RevocationReason::orderByDesc('updated_at')->pluck('updated_at')->first();
                return strtotime( strval( $updated_at));

                break;
            case TypeParametrics::PAISES:
                $updated_at = Country::orderByDesc('updated_at')->pluck('updated_at')->first();
                return strtotime( strval( $updated_at));

                break;
            case TypeParametrics::TIPOS_DOCUMENTO_IDENTIDAD:
                $updated_at = IdentityDocumentType::orderByDesc('updated_at')->pluck('updated_at')->first();
                return strtotime( strval( $updated_at));

                break;
            case TypeParametrics::METODOS_DE_PAGO:
                $updated_at = PaymentMethod::orderByDesc('updated_at')->pluck('updated_at')->first();
                return strtotime( strval( $updated_at));

                break;
            case TypeParametrics::MONEDAS:
                $updated_at = Currency::orderByDesc('updated_at')->pluck('updated_at')->first();
                return strtotime( strval( $updated_at));

                break;
            case TypeParametrics::UNIDADES:
                $updated_at = Unit::orderByDesc('updated_at')->pluck('updated_at')->first();
                return strtotime( strval( $updated_at));

                break;
            case TypeParametrics::TIPOS_HABITACION:
                $updated_at = FelRoomType::orderByDesc('updated_at')->pluck('updated_at')->first();
                return strtotime( strval( $updated_at));

                break;
            
            default:
                throw new ClientFelException("No existe el tipo este metodo");
                break;
        }
    }

    public static function saveParametrics($type, $company_id, $data){
        $stats = ['upserted' => 0, 'deleted' => 0, 'unlinked' => 0];

        try {
            if ($type === TypeParametrics::PRODUCTOS_SIN) {
                $existing = SINProduct::where('company_id', $company_id)->get()->keyBy(function($i) { return $i->codigo . '_' . $i->codigoActividad; });
                $toInsert = []; $toUpdate = []; $toDelete = [];
                foreach ($data as $d) {
                    $key = $d['codigo'] . '_' . $d['codigoActividad'];
                    if (isset($d['isActive']) && $d['isActive'] == false) {
                        $toDelete[] = $d['codigo'];
                    } else {
                        if ($existing->has($key)) {
                            if ($existing[$key]->descripcion != $d['descripcion']) {
                                $toUpdate[] = ['id' => $existing[$key]->id, 'descripcion' => $d['descripcion']];
                            }
                        } else {
                            $toInsert[] = [
                                'company_id' => $company_id, 'codigo' => $d['codigo'], 'codigoActividad' => $d['codigoActividad'],
                                'descripcion' => $d['descripcion'], 'created_at' => Carbon::now()->toDateTimeString(), 'updated_at' => Carbon::now()->toDateTimeString()
                            ];
                        }
                    }
                }
                if (!empty($toDelete)) {
                    SINProduct::where('company_id', $company_id)->whereIn('codigo', $toDelete)->delete();
                    $stats['unlinked'] += \EmizorIpx\ClientFel\Models\FelSyncProduct::where('company_id', $company_id)->whereIn('codigo_producto_sin', $toDelete)->update(['codigo_producto_sin' => null]);
                    $stats['deleted'] += count($toDelete);
                }
                if (!empty($toInsert)) {
                    foreach (array_chunk($toInsert, 500) as $chunk) { SINProduct::insert($chunk); }
                    $stats['upserted'] += count($toInsert);
                }
                foreach ($toUpdate as $up) { SINProduct::where('id', $up['id'])->update(['descripcion' => $up['descripcion'], 'updated_at' => Carbon::now()->toDateTimeString()]); $stats['upserted']++; }

            } elseif ($type === TypeParametrics::LEYENDAS) {
                $existing = FelCaption::where('company_id', $company_id)->get()->keyBy(function($i) { return $i->codigo . '_' . $i->codigoActividad; });
                $toInsert = []; $toUpdate = []; $toDelete = []; $toDeleteAct = [];
                foreach ($data as $d) {
                    $key = $d['codigo'] . '_' . $d['codigoActividad'];
                    if (isset($d['isActive']) && $d['isActive'] == false) {
                        $toDelete[] = $d['codigo']; $toDeleteAct[] = $d['codigoActividad'];
                    } else {
                        if ($existing->has($key)) {
                            if ($existing[$key]->descripcion != $d['descripcion']) {
                                $toUpdate[] = ['id' => $existing[$key]->id, 'descripcion' => $d['descripcion']];
                            }
                        } else {
                            $toInsert[] = [
                                'company_id' => $company_id, 'codigo' => $d['codigo'], 'codigoActividad' => $d['codigoActividad'],
                                'descripcion' => $d['descripcion'], 'created_at' => Carbon::now()->toDateTimeString(), 'updated_at' => Carbon::now()->toDateTimeString()
                            ];
                        }
                    }
                }
                if (!empty($toDelete)) {
                    // we should delete by matching both but for simplicity delete whereIn
                    foreach($data as $d) { if (isset($d['isActive']) && $d['isActive'] == false) { FelCaption::where('company_id', $company_id)->where('codigo', $d['codigo'])->where('codigoActividad', $d['codigoActividad'])->delete(); $stats['deleted']++; } }
                }
                if (!empty($toInsert)) { foreach (array_chunk($toInsert, 500) as $chunk) { FelCaption::insert($chunk); } $stats['upserted'] += count($toInsert); }
                foreach ($toUpdate as $up) { FelCaption::where('id', $up['id'])->update(['descripcion' => $up['descripcion'], 'updated_at' => Carbon::now()->toDateTimeString()]); $stats['upserted']++; }

            } elseif ($type === TypeParametrics::ACTIVIDADES) {
                $existing = FelActivity::where('company_id', $company_id)->get()->keyBy('codigo');
                $toInsert = []; $toUpdate = []; $toDelete = [];
                foreach ($data as $d) {
                    $key = $d['codigo'];
                    if (isset($d['isActive']) && $d['isActive'] == false) {
                        $toDelete[] = $d['codigo'];
                    } else {
                        if ($existing->has($key)) {
                            if ($existing[$key]->descripcion != $d['descripcion'] || $existing[$key]->tipoActividad != $d['tipoActividad']) {
                                $toUpdate[] = ['id' => $existing[$key]->id, 'descripcion' => $d['descripcion'], 'tipoActividad' => $d['tipoActividad']];
                            }
                        } else {
                            $toInsert[] = [
                                'company_id' => $company_id, 'codigo' => $d['codigo'], 'tipoActividad' => $d['tipoActividad'],
                                'descripcion' => $d['descripcion'], 'created_at' => Carbon::now()->toDateTimeString(), 'updated_at' => Carbon::now()->toDateTimeString()
                            ];
                        }
                    }
                }
                if (!empty($toDelete)) {
                    FelActivity::where('company_id', $company_id)->whereIn('codigo', $toDelete)->delete();
                    $stats['unlinked'] += \EmizorIpx\ClientFel\Models\FelSyncProduct::where('company_id', $company_id)->whereIn('codigo_actividad_economica', $toDelete)->update(['codigo_actividad_economica' => null]);
                    $stats['deleted'] += count($toDelete);
                }
                if (!empty($toInsert)) { foreach (array_chunk($toInsert, 500) as $chunk) { FelActivity::insert($chunk); } $stats['upserted'] += count($toInsert); }
                foreach ($toUpdate as $up) { FelActivity::where('id', $up['id'])->update(['descripcion' => $up['descripcion'], 'tipoActividad' => $up['tipoActividad'], 'updated_at' => Carbon::now()->toDateTimeString()]); $stats['upserted']++; }

            } elseif ($type === TypeParametrics::ACTIVIDADES_DOCUMENTO_SECTOR) {
                $existing = FelActivityDocumentSector::where('company_id', $company_id)->get()->keyBy(function($i) { return $i->codigoDocumentoSector . '_' . $i->codigoActividad; });
                $toInsert = []; $toUpdate = []; $toDelete = [];
                foreach ($data as $d) {
                    $key = $d['codigo'] . '_' . $d['codigoActividad'];
                    if (isset($d['isActive']) && $d['isActive'] == false) {
                        $toDelete[] = $d;
                    } else {
                        if ($existing->has($key)) {
                            if ($existing[$key]->actividad != $d['actividad'] || $existing[$key]->documentoSector != $d['descripcion']) {
                                $toUpdate[] = ['id' => $existing[$key]->id, 'actividad' => $d['actividad'], 'documentoSector' => $d['descripcion'], 'tipoDocumentoSector' => $d['tipoDocumentoSector']];
                            }
                        } else {
                            $toInsert[] = [
                                'company_id' => $company_id, 'codigoDocumentoSector' => $d['codigo'], 'codigoActividad' => $d['codigoActividad'],
                                'actividad' => $d['actividad'], 'documentoSector' => $d['descripcion'], 'tipoDocumentoSector' => $d['tipoDocumentoSector'],
                                'created_at' => Carbon::now()->toDateTimeString(), 'updated_at' => Carbon::now()->toDateTimeString()
                            ];
                        }
                    }
                }
                if (!empty($toDelete)) {
                    foreach($toDelete as $d) { FelActivityDocumentSector::where('company_id', $company_id)->where('codigoDocumentoSector', $d['codigo'])->where('codigoActividad', $d['codigoActividad'])->delete(); $stats['deleted']++; }
                }
                if (!empty($toInsert)) { foreach (array_chunk($toInsert, 500) as $chunk) { FelActivityDocumentSector::insert($chunk); } $stats['upserted'] += count($toInsert); }
                foreach ($toUpdate as $up) { FelActivityDocumentSector::where('id', $up['id'])->update(['actividad' => $up['actividad'], 'documentoSector' => $up['documentoSector'], 'tipoDocumentoSector' => $up['tipoDocumentoSector'], 'updated_at' => Carbon::now()->toDateTimeString()]); $stats['upserted']++; }

            } else {
                // Fallback for smaller parametrics
                foreach ($data as $item) {
                    $result = static::createOrUpdate($type, $item, $company_id);
                    if (isset($result['action'])) {
                        if ($result['action'] == 'deleted') { $stats['deleted']++; } else { $stats['upserted']++; }
                    }
                    if (isset($result['unlinked'])) { $stats['unlinked'] += $result['unlinked']; }
                }
            }
        } catch (ClientFelException $ex) {
            \Log::debug("Error to save parametric ". $ex->getMessage());
        }
        
        \EmizorIpx\ClientFel\Models\BitacoraLog::register(
            'INFO', 
            'SYNC_PARAMETRICS', 
            "Empresa [$company_id] | Paramétricas $type procesadas en Batch. Agregadas/Actualizadas: {$stats['upserted']}. Eliminadas: {$stats['deleted']}. Productos desvinculados: {$stats['unlinked']}."
        );
    }
}