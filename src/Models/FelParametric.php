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
                $query = FelCaption::whereCompanyId($company_id)->orderBy('descripcion');
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
}
