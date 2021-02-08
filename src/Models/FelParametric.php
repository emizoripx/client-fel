<?php

namespace EmizorIpx\ClientFel\Models;

use EmizorIpx\ClientFel\Exceptions\ClientFelException;
use EmizorIpx\ClientFel\Models\Parametric\Country;
use EmizorIpx\ClientFel\Models\Parametric\Currency;
use EmizorIpx\ClientFel\Models\Parametric\IdentityDocumentType;
use EmizorIpx\ClientFel\Models\Parametric\PaymentMethod;
use EmizorIpx\ClientFel\Models\Parametric\RevocationReason;
use EmizorIpx\ClientFel\Models\Parametric\Unit;
use EmizorIpx\ClientFel\Utils\TypeParametrics;


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
                    $data_[] = $d;
                }
                return FelActivity::insert($data_);
                break;
            case TypeParametrics::LEYENDAS:
                $data_ = array();
                foreach($data as $d) { 
                    $d["company_id"] = $company_id;
                    $d["codigo"] = $d['codigoActividad'];
                    $d["descripcion"] = $d['descripcion'];
                    unset($d['codigoActividad']);
                    $data_[] = $d;
                }
                
                return FelCaption::insert($data_);
                break;
            case TypeParametrics::MONEDAS:
                return Currency::insert($data);
                break;

            case TypeParametrics::METODOS_DE_PAGO:
                return PaymentMethod::insert($data);
                break;
            case TypeParametrics::PAISES:
                return Country::insert($data);
                break;
            case TypeParametrics::TIPOS_DOCUMENTO_IDENTIDAD:
                return IdentityDocumentType::insert($data);
                break;
            case TypeParametrics::MOTIVO_ANULACION:
                return RevocationReason::insert($data);
                break;
            case TypeParametrics::UNIDADES:
                return Unit::insert($data);
                break;
            default:
                throw new ClientFelException("No existe el tipo este metodo");
                break;
        }
    }

    public static function index($type, $company_id)
    {
        switch ($type) {
            case TypeParametrics::ACTIVIDADES:
                return FelActivity::whereCompanyId($company_id)->get();
                break;
            case TypeParametrics::LEYENDAS:
                return FelCaption::whereCompanyId($company_id)->get();
                break;
            case TypeParametrics::MONEDAS:
                return Currency::all();
                break;
            case TypeParametrics::METODOS_DE_PAGO:
                return PaymentMethod::all();
                break;
            case TypeParametrics::PAISES:
                return Country::all();
                break;
            case TypeParametrics::TIPOS_DOCUMENTO_IDENTIDAD:
                return IdentityDocumentType::all();
                break;
            case TypeParametrics::MOTIVO_ANULACION:
                return RevocationReason::all();
                break;
            case TypeParametrics::UNIDADES:
                return Unit::all();
                break;
            default:
                throw new ClientFelException("No existe el tipo este metodo");
                break;
        }
    }
}
