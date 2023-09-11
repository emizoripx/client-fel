<?php

namespace EmizorIpx\ClientFel\Reports\Invoices;

use EmizorIpx\ClientFel\Reports\BaseReport;
use EmizorIpx\ClientFel\Reports\ReportInterface;

class RegisterSalesReport extends BaseReport implements ReportInterface
{

    protected $branch_code;

    protected $type_document;

    protected $company_id;

    protected $revocated_zero;

    protected $user;

    protected $columns;

    public function __construct($company_id, $request, $columns, $user)
    {
        $this->company_id = $company_id;

        $this->columns = $columns;

        $this->user = $user;

        $this->type_document = $request->has('type_document') ? $request->get('type_document') : null;
        
        $this->branch_code = $request->has('branch') ? $request->get('branch') : null;

        $this->revocated_zero = $request->has('revocated_zero') ? $request->get('revocated_zero') : false;
        $this->from = $request->has('from_date') ? $request->get('from_date') : null;
        $this->to = $request->has('to_date') ? $request->get('to_date') : null;

        parent::__construct($this->from, $this->to);
    }

    public function addBranchFilter($query)
    {

        if (!is_null($this->branch_code)) {

            \Log::debug("Filter by Brach: " . $this->branch_code);

            $this->branch_desc = "Sucursal " . $this->branch_code;

            return $query->where('fel_invoice_requests.codigoSucursal', $this->branch_code);
        } elseif (count($branch_access = $this->user->getOnlyBranchAccess()) > 0) {

            $branch_access = $this->user->getOnlyBranchAccess();

            \Log::debug("Filter by Access Branch");

            $branches_desc = [];
            foreach ($branch_access as $value) {
                array_push($branches_desc, ($value == 0 ? " Casa Matriz" : " Sucursal " . $value));
            }

            $this->branch_desc = implode(" - ", $branches_desc);

            return $query->whereIn('fel_invoice_requests.codigoSucursal', $branch_access);
        }

        return $query;
    }

    public function addSelectColumns($query)
    {
  
        if ( $this->revocated_zero ) {
            $query->selectRaw('(@counter := @counter +1) as num,"2" as especificaciones, fechaEmision,numeroFactura,cuf,numeroDocumento,complemento,nombreRazonSocial, if(codigoEstado=690 ||  codigoEstado=908,round(montoTotal+descuentoAdicional+montoGiftCard,2),"0.00") as importeTotal, "0.00" as importeIce,"0.00" as importeIEHD, "0.00" as importeIPJ,"0.00" as tasas,"0.00" as otros,"0.00" as exportaciones,"0.00" as tasaCero, if(codigoEstado=690 ||  codigoEstado=908,round(montoTotal,2),"0.00") as subTotal, if(codigoEstado=690 ||  codigoEstado=908,descuentoAdicional,"0.00") as descuentoAdicional, if(codigoEstado=690 ||  codigoEstado=908,round(montoGiftCard,2),"0.00") as montoGiftCard, if(codigoEstado=690 ||  codigoEstado=908,round(montoTotalSujetoIva,2),"0.00") as baseCreditoFiscal, if(codigoEstado=690 ||  codigoEstado=908,round(montoTotalSujetoIva*0.13,2),"0.00") as debitoFiscal, if(codigoEstado=690 ||  codigoEstado=908,"V","A") as estado, "" as codigo_control, "0" as tipoVenta');
        } else {
            $query->selectRaw('(@counter := @counter +1) as num,"2" as especificaciones, fechaEmision,numeroFactura,cuf,numeroDocumento,complemento,nombreRazonSocial,round(montoTotal+descuentoAdicional+montoGiftCard,2) as importeTotal,"0.00" as importeIce,"0.00" as importeIEHD, "0.00" as importeIPJ,"0.00" as tasas,"0.00" as otros,"0.00" as exportaciones,"0.00" as tasaCero,round(montoTotal,2) as subTotal, descuentoAdicional,round(montoGiftCard,2) as montoGiftCard, round(montoTotalSujetoIva,2) as baseCreditoFiscal,round(montoTotalSujetoIva*0.13,2) as debitoFiscal, if(codigoEstado=690 ||  codigoEstado=908,"V","A") as estado, "" as codigo_control, "0" as tipoVenta');
        }
        
        return $query;
    }

    public function generateReport()
    {
        \DB::statement(\DB::raw("set @counter := 0"));
        $query_invoices = \DB::table('fel_invoice_requests')
                        ->where('fel_invoice_requests.company_id', $this->company_id)
                        ->whereNotNull('fel_invoice_requests.codigoEstado')
                        ->whereNotNull('fel_invoice_requests.cuf');   
        $query_invoices = $this->addDateFilter($query_invoices);
        $query_invoices = $this->addBranchFilter($query_invoices);
        $query_invoices =  $this->addSelectColumns($query_invoices);


        return [
            "header" => [
                "Nº",
                "ESPECIFICACION",
                "FECHA DE LA FACTURA",
                "N° DE LA FACTURA",
                "CODIGO DE AUTORIZACION",
                "NIT / CI CLIENTE",
                "COMPLEMENTO",
                "NOMBRE O RAZON SOCIAL",
                "IMPORTE TOTAL DE LA VENTA",
                "IMPORTE ICE",
                "IMPORTE IEHD",
                "IMPORTE IPJ",
                "TASAS",
                "OTROS NO SUJETOS AL IVA",
                "EXPORTACIONES Y OPERACIONES EXENTAS",
                "VENTAS GRAVADAS A TASA CERO",
                "SUBTOTAL", 
                "DESCUENTOS, BONIFICACIONES Y REBAJAS SUJETAS AL IVA",
                "IMPORTE GIFT CARD",
                "IMPORTE BASE PARA DEBITO FISCAL",
                "DEBITO FISCAL",
                "ESTADO",
                "CODIGO DE CONTROL",
                "TIPO DE VENTA"
            ],
            "invoices" => $query_invoices
        ];
    }
}
