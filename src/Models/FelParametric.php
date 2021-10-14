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
        \Log::debug($data_added);
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
                $query = Currency::orderBy('descripcion');
                break;
            case TypeParametrics::METODOS_DE_PAGO:
                $query = PaymentMethod::orderBy('descripcion');
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
            
            default:
                throw new ClientFelException("No existe el tipo de parametrica");
                break;
        }
    }

    public static function createOrUpdate($type, $data, $company_id = null){

        switch ($type) {
            case TypeParametrics::ACTIVIDADES:

                    $felActivity = FelActivity::where('company_id', $company_id)->where('codigo', $data['codigo'])->first();

                    if(is_null($felActivity)){

                        unset($data['isActive']);
                        static::create($type, [$data], $company_id);
                        \Log::debug("Saved Actividad codigo #". $data['codigo']);
                    }
                    elseif ( !is_null($felActivity) && $data['isActive'] == false) {
                        $felActivity->delete();
                        \Log::debug("Delete Actividad codigo #". $data['codigo']);
                    } 
                    else{
                        \Log::debug("updating Actividad codigo #". $data['codigo']);
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
                        \Log::debug("Saved Leyenda codigo #". $data['codigo']);
                    }
                    elseif (!is_null($felCaption) && $data['isActive'] == false) {
                        \Log::debug("Delete Leyenda codigo #". $data['codigo']);
                        $felCaption->delete();
                    }
                    else{
                        \Log::debug("updating Leyenda codigo #". $data['codigo']);
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
                    \Log::debug("Saved Producto SIN codigo #". $data['codigo']);
                }
                elseif (!is_null($sinProduct) && $data['isActive'] == false) {
                    $sinProduct->delete();
                    \Log::debug("Delete Producto SIN codigo #". $data['codigo']);
                }
                else {
                    \Log::debug("updating Producto SIN codigo #". $data['codigo']);
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
                    \Log::debug("Saved Actividad Doc Sector codigo #". $data['codigo']);
                }
                elseif (!is_null($felActivityDocumentSector) && $data['isActive'] == false) {
                    $felActivityDocumentSector->delete();
                    \Log::debug("Delete Actividad Doc Sector codigo #". $data['codigo']);
                }
                else {
                    \Log::debug("updating Actividad Doc Sector codigo #". $data['codigo']);
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
                    \Log::debug("Saved SectorDocument codigo #". $data['codigoDocumentSector']);
                }
                elseif (!is_null($felSectorDocument) && $data['isActive'] == false) {
                    $felSectorDocument->delete();
                    \Log::debug("Delete SectorDocument codigo #". $data['codigoDocumentSector']);
                }
                else {
                    \Log::debug("updating SectorDocument codigo #". $data['codigoDocumentSector']);
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
                    \Log::debug("Saved Motivo Anulacion codigo #". $data['codigo']);
                }
                elseif (!is_null($motivoAnulacion) && $data['isActive'] == false) {
                    $motivoAnulacion->delete();
                    \Log::debug("Delete Motivo Anulacion codigo #". $data['codigo']);
                }
                else {
                    \Log::debug("updating Motivo Anulacion codigo #". $data['codigo']);
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
                    \Log::debug("Saved Paises codigo #". $data['codigo']);
                }
                elseif (!is_null($pais) && $data['isActive'] == false) {
                    $pais->delete();
                    \Log::debug("Delete Paises codigo #". $data['codigo']);
                }
                else {
                    \Log::debug("updating Paises codigo #". $data['codigo']);
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
                    \Log::debug("Saved documentIdentidad codigo #". $data['codigo']);
                }
                elseif (!is_null($documentIdentidad) && $data['isActive'] == false) {
                    $documentIdentidad->delete();
                    \Log::debug("Delete documentIdentidad codigo #". $data['codigo']);
                }
                else {
                    \Log::debug("updating documentIdentidad codigo #". $data['codigo']);
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
                    \Log::debug("Saved metodosPago codigo #". $data['codigo']);
                }
                elseif (!is_null($metodosPago) && $data['isActive'] == false) {
                    $metodosPago->delete();
                    \Log::debug("Delete metodosPago codigo #". $data['codigo']);
                }
                else {
                    \Log::debug("updating metodosPago codigo #". $data['codigo']);
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
                    \Log::debug("Saved currency codigo #". $data['codigo']);
                }
                elseif (!is_null($currency) && $data['isActive'] == false) {
                    $currency->delete();
                    \Log::debug("Delete currency codigo #". $data['codigo']);
                }
                else {
                    \Log::debug("updating currency codigo #". $data['codigo']);
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
                    \Log::debug("Saved unit codigo #". $data['codigo']);
                }
                elseif (!is_null($unit) && $data['isActive'] == false) {
                    $unit->delete();
                    \Log::debug("Delete unit codigo #". $data['codigo']);
                }
                else {
                    \Log::debug("updating unit codigo #". $data['codigo']);
                    $unit->update([
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

    }

    public static function getUpdatedAt($type, $company_id){
        switch ($type) {
            case TypeParametrics::ACTIVIDADES:
                $updated_at = FelActivity::where('company_id', $company_id)->orderByDesc('updated_at')->pluck('updated_at')->first();
                \Log::debug("Get updated_at: ". $updated_at);
                return strtotime($updated_at);
                
                break;
            case TypeParametrics::LEYENDAS:
                $updated_at = FelCaption::where('company_id', $company_id)->orderByDesc('updated_at')->pluck('updated_at')->first();
                \Log::debug("Get updated_at: " .strtotime($updated_at));
                return strtotime( $updated_at );

                break;
            case TypeParametrics::PRODUCTOS_SIN:
                $updated_at = SINProduct::where('company_id', $company_id)->orderByDesc('updated_at')->pluck('updated_at')->first();
                \Log::debug("Get updated_at: ". $updated_at);
                return strtotime($updated_at);

                break;
            case TypeParametrics::ACTIVIDADES_DOCUMENTO_SECTOR:
                $updated_at = FelActivityDocumentSector::where('company_id', $company_id)->orderByDesc('updated_at')->pluck('updated_at')->first();
                \Log::debug("Get updated_at: ". strtotime( strval( $updated_at)));
                return strtotime( strval( $updated_at));

                break;
            case TypeParametrics::TIPOS_DOCUMENTO_SECTOR:
                $updated_at = SectorDocumentTypes::where('company_id', $company_id)->orderByDesc('updated_at')->pluck('updated_at')->first();
                \Log::debug("Get updated_at: ". strtotime( strval( $updated_at)));
                return strtotime( strval( $updated_at));

                break;
            case TypeParametrics::MOTIVO_ANULACION:
                $updated_at = RevocationReason::orderByDesc('updated_at')->pluck('updated_at')->first();
                \Log::debug("Get updated_at: ". strtotime( strval( $updated_at)));
                return strtotime( strval( $updated_at));

                break;
            case TypeParametrics::PAISES:
                $updated_at = Country::orderByDesc('updated_at')->pluck('updated_at')->first();
                \Log::debug("Get updated_at: ". strtotime( strval( $updated_at)));
                return strtotime( strval( $updated_at));

                break;
            case TypeParametrics::TIPOS_DOCUMENTO_IDENTIDAD:
                $updated_at = IdentityDocumentType::orderByDesc('updated_at')->pluck('updated_at')->first();
                \Log::debug("Get updated_at: ". strtotime( strval( $updated_at)));
                return strtotime( strval( $updated_at));

                break;
            case TypeParametrics::METODOS_DE_PAGO:
                $updated_at = PaymentMethod::orderByDesc('updated_at')->pluck('updated_at')->first();
                \Log::debug("Get updated_at: ". strtotime( strval( $updated_at)));
                return strtotime( strval( $updated_at));

                break;
            case TypeParametrics::MONEDAS:
                $updated_at = Currency::orderByDesc('updated_at')->pluck('updated_at')->first();
                \Log::debug("Get updated_at: ". strtotime( strval( $updated_at)));
                return strtotime( strval( $updated_at));

                break;
            case TypeParametrics::UNIDADES:
                $updated_at = Unit::orderByDesc('updated_at')->pluck('updated_at')->first();
                \Log::debug("Get updated_at: ". strtotime( strval( $updated_at)));
                return strtotime( strval( $updated_at));

                break;
            
            default:
                throw new ClientFelException("No existe el tipo este metodo");
                break;
        }
    }

    public static function saveParametrics($type, $company_id, $data){
        foreach ($data as $item) {
            try {
                static::createOrUpdate($type, $item, $company_id);
                \Log::debug("Saved Parametric");
            } catch (ClientFelException $ex) {
                \Log::debug("Error to save parametric ". $ex->getMessage());
            }
        }
    }
}
