<?php

namespace EmizorIpx\ClientFel\Traits;

use App\Utils\Number;
use EmizorIpx\ClientFel\Models\Parametric\Country;
use EmizorIpx\ClientFel\Models\Parametric\Currency;
use EmizorIpx\ClientFel\Models\Parametric\Unit;
use EmizorIpx\ClientFel\Utils\Currencies;
use EmizorIpx\ClientFel\Utils\TypeDocumentSector;

trait HtmlDesignTrait{

    

    public function makeRowsProductExportacionMinerales(){
        $felInvoice = $this->fel_invoice;

        $rows_table = '';
        
            
            $rows_table = $rows_table.'
            <tr>
                <td style="padding-top: 10px; padding-bottom:10px;" class="b-solid-left center bold">HUMEDAD </td>
                <td style="padding-top: 10px; padding-bottom:10px;" class="b-solid-right center">'.number_format((float)$felInvoice->humedadPorcentaje,2,',','.').' % </td>
                <td style="padding-top: 10px; padding-bottom:10px;" class="b-solid center">'.number_format((float)$this->fel_invoice->humedadValor,2,',','.').' </td>
                <td style="padding-top: 10px; padding-bottom:10px;" class="b-solid center">'.(isset($felInvoice->detalles[0]['codigoProducto']) ? ($felInvoice->detalles[0]['codigoProducto']) : '') .' </td>
                <td style="padding-top: 10px; padding-bottom:10px;" class="b-solid center">'.(isset($felInvoice->detalles[0]['descripcionLeyes']) ? $felInvoice->detalles[0]['descripcionLeyes'] : null) .' </td>
                <td style="padding-top: 10px; padding-bottom:10px;" class="b-solid center">'.(isset($felInvoice->detalles[0]['codigoNandina']) ? $felInvoice->detalles[0]['codigoNandina'] : null) .'</td>
                <td style="padding-top: 10px; padding-bottom:10px;" class="b-solid center">'.(isset($felInvoice->detalles[0]['cantidad']) ? number_format((float)$felInvoice->detalles[0]['cantidad'] ?? 0,5,',','.').' '.Unit::getUnitDescription($felInvoice->detalles[0]['unidadMedida']) : null).'</td>
                <td style="padding-top: 10px; padding-bottom:10px;" class="b-solid center">'.(isset($felInvoice->detalles[0]['precioUnitario']) ? number_format((float)$felInvoice->detalles[0]['precioUnitario'] ?? 0,5,',','.').'  '. Currencies::getShortCode($felInvoice->codigoMoneda).'/'.Unit::getUnitDescription($felInvoice->detalles[0]['unidadMedida']) : null ).'</td>
                
            </tr>
            <tr>
                <td style="padding-top: 10px; padding-bottom:10px;" class="b-solid-left center bold">MERMA </td>
                <td style="padding-top: 10px; padding-bottom:10px;" class="b-solid-right center">'.number_format((float)$felInvoice->mermaPorcentaje,2,',','.').' % </td>
                <td style="padding-top: 10px; padding-bottom:10px;" class="b-solid center">'.number_format((float)$this->fel_invoice->mermaValor,2,',','.').' </td>
                <td style="padding-top: 10px; padding-bottom:10px;" class="b-solid center">'.(isset($felInvoice->detalles[1]['codigoProducto']) ? ($felInvoice->detalles[1]['codigoProducto']) : '') .' </td>
                <td style="padding-top: 10px; padding-bottom:10px;" class="b-solid center">'.(isset($felInvoice->detalles[1]['descripcionLeyes']) ? $felInvoice->detalles[1]['descripcionLeyes'] : null) .' </td>
                <td style="padding-top: 10px; padding-bottom:10px;" class="b-solid center">'.(isset($felInvoice->detalles[1]['codigoNandina']) ? $felInvoice->detalles[1]['codigoNandina'] : null) .'</td>
                <td style="padding-top: 10px; padding-bottom:10px;" class="b-solid center">'.(isset($felInvoice->detalles[1]['cantidad']) ? number_format((float)$felInvoice->detalles[1]['cantidad'] ?? 0,5,',','.').' '.Unit::getUnitDescription($felInvoice->detalles[1]['unidadMedida']) : null).'</td>
                <td style="padding-top: 10px; padding-bottom:10px;" class="b-solid center">'.(isset($felInvoice->detalles[1]['precioUnitario']) ? number_format((float)$felInvoice->detalles[1]['precioUnitario'] ?? 0,5,',','.').'  '. Currencies::getShortCode($felInvoice->codigoMoneda).'/'.Unit::getUnitDescription($felInvoice->detalles[1]['unidadMedida']) : null ).'</td>
                
            </tr>
            <tr>
                <td style="padding-top: 10px; padding-bottom:10px;" class="b-solid-left center bold">KILOS NETOS SECOS </td>
                <td style="padding-top: 10px; padding-bottom:10px;" class="b-solid-right center"> </td>
                <td style="padding-top: 10px; padding-bottom:10px;" class="b-solid center">'.number_format((float)$this->fel_invoice->kilosNetosSecos,2,',','.').' </td>
                <td style="padding-top: 10px; padding-bottom:10px;" class="b-solid center">'. (isset($felInvoice->detalles[2]['codigoProducto']) ? ($felInvoice->detalles[2]['codigoProducto']) : '') .' </td>
                <td style="padding-top: 10px; padding-bottom:10px;" class="b-solid center">'. (isset($felInvoice->detalles[2]['descripcionLeyes']) ? $felInvoice->detalles[2]['descripcionLeyes'] : null) .' </td>
                <td style="padding-top: 10px; padding-bottom:10px;" class="b-solid center">'. (isset($felInvoice->detalles[2]['codigoNandina']) ? $felInvoice->detalles[2]['codigoNandina'] : null) .'</td>
                <td style="padding-top: 10px; padding-bottom:10px;" class="b-solid center">'. (isset($felInvoice->detalles[2]['cantidad']) ? number_format((float)$felInvoice->detalles[2]['cantidad'] ?? 0,5,',','.').' '.Unit::getUnitDescription($felInvoice->detalles[2]['unidadMedida']) : null).'</td>
                <td style="padding-top: 10px; padding-bottom:10px;" class="b-solid center">'.  (isset($felInvoice->detalles[2]['precioUnitario']) ? number_format((float)$felInvoice->detalles[2]['precioUnitario'] ?? 0,5,',','.').'  '. Currencies::getShortCode($felInvoice->codigoMoneda).'/'.Unit::getUnitDescription($felInvoice->detalles[2]['unidadMedida']) : null ).'</td>
                
            </tr>
            ';

        return $rows_table;
    }

    public function MakeSubtotalsRows(){
        $rows = (isset($this->fel_invoice->detalles[0]['descripcion']) ? 
        '<tr>
            <th class="left-align" colspan="7">'.strtoupper($this->fel_invoice->detalles[0]['descripcion']).'</th>
            <td class="b-solid right-align">'.  number_format((float)$this->fel_invoice->detalles[0]['subTotal'] ,2,',','.').'</td>
        </tr>' : '')
        
        .(isset($this->fel_invoice->detalles[1]['descripcion']) ? '
        <tr>
            <th class="left-align" colspan="7">'.strtoupper($this->fel_invoice->detalles[1]['descripcion']).'</th>
            <td class="b-solid right-align">'.  number_format((float)$this->fel_invoice->detalles[1]['subTotal'] ,2,',','.').'</td>
        </tr>' : '')
        . (isset($this->fel_invoice->detalles[2]['descripcion']) ? '
        <tr>
            <th class="left-align" colspan="7">'.strtoupper($this->fel_invoice->detalles[2]['descripcion']).'</th>
            <td class="b-solid right-align">'.  number_format((float)$this->fel_invoice->detalles[2]['subTotal'] ,2,',','.').'</td>
        </tr>' : ''
        );
        return $rows;
    }

    public function appendFieldExportacionMinerales($data){


            $data['$fel.invoice_title'] = ['value' => $this->cuf ? 'FACTURA EXPORTACIÓN' : 'PREFACTURA EXPORTACIÓN', 'label' => 'Titulo'];
            $data['$fel.invoice_type'] = ['value' => $this->cuf ? 'Factura sin derecho a Crédito Fiscal' : '', 'label' => 'Tipo de Factura'];
            $data['$fel.ruex'] = ['value' => $this->fel_invoice->ruex , 'label' => 'RUEX'];
            $data['$fel.nim'] = ['value' => $this->fel_invoice->nim , 'label' => 'NIM'];
            $data['$fel.direccion_comprador'] = ['value' => $this->fel_invoice->direccionComprador, 'label' => 'Dirección Comprador'];
            $data['$fel.concentrado_granel'] = ['value' => $this->fel_invoice->concentradoGranel, 'label' => 'Concentrado Granel'];
            $data['$fel.puerto_transito'] = ['value' => $this->fel_invoice->puertoTransito, 'label' => 'Puerto Transito'];
            $data['$fel.puerto_destino'] = ['value' => $this->fel_invoice->puertoDestino, 'label' => 'Puerto Destino'];
            $data['$fel.origen'] = ['value' => $this->fel_invoice->origen, 'label' => 'Origen'];
            $data['$fel.incoterm'] = ['value' => $this->fel_invoice->incoterm, 'label' => 'INCOTERM'];
            $data['$fel.pais_destino'] = ['value' => Country::getDescriptionCountry($this->fel_invoice->paisDestino), 'label' => 'País Destino'];
            $data['$fel.moneda_transaccion'] = ['value' => Currency::getCurrecyDescription($this->fel_invoice->codigoMoneda), 'label' => 'Moneda Transacción'];
            $data['$fel.moneda_code'] = ['value' => Currencies::getShortCode($this->fel_invoice->codigoMoneda), 'label' => 'Código Moneda'];
            $data['$fel.tipo_cambio'] = ['value' => number_format((float)$this->fel_invoice->tipoCambio,5,',','.'), 'label' => 'Tipo Cambio Oficial'];
            $data['$fel.tipo_cambioANB'] = ['value' => number_format((float)$this->fel_invoice->tipoCambioANB,5,',','.'), 'label' => 'Tipo Cambio ANB'];
            $data['$fel.numero_lote'] = ['value' => $this->fel_invoice->numeroLote, 'label' => 'Número Lote'];
            $data['$fel.kilos_netosHumedos'] = ['value' => number_format((float)$this->fel_invoice->kilosNetosHumedos,2,',','.'), 'label' => 'Kilos Netos Húmedos'];
            $data['$fel.humedad_porcentaje'] = ['value' => (int) $this->fel_invoice->humedadPorcentaje, 'label' => 'Humedad Porcentaje'];
            $data['$fel.humedad_valor'] = ['value' => number_format((float)$this->fel_invoice->humedadValor,2,',','.'), 'label' => 'Humedad Valor'];
            $data['$fel.merma_porcentaje'] = ['value' => (int) $this->fel_invoice->mermaPorcentaje, 'label' => 'Merma Porcentaje'];
            $data['$fel.merma_valor'] = ['value' => number_format((float)$this->fel_invoice->mermaValor,2,',','.'), 'label' => 'Merma Valor'];
            $data['$fel.kilos_netosSecos'] = ['value' => number_format((float)$this->fel_invoice->kilosNetosSecos,2,',','.'), 'label' => 'Kilos Netos Secos'];
            $data['$fel.gastos_realizacion'] = ['value' => number_format((float)$this->fel_invoice->gastosRealizacion,2,',','.'), 'label' => 'Gastos Realización'];
            $data['$fel.valor_FOBFrontera'] = ['value' => number_format((float)$this->fel_invoice->otrosDatos['valorFobFrontera'],2,',','.') , 'label' => 'Valor FOB Frontera'];
            $data['$fel.valor_FOBFronteraBs'] = ['value' => number_format((float)$this->fel_invoice->otrosDatos['valorFobFronteraBs'],2,',','.'), 'label' => 'Valor FOB Frontera'];
            $data['$fel.valor_FOBFronteraLiteral'] = ['value' => $this->getToWord((float)$this->fel_invoice->otrosDatos['valorFobFrontera'], 2, 'Dólares Americanos'), 'label' => 'Valor FOB Frontera Literal'];
            $data['$fel.valor_FOBFronteraBsLiteral'] = ['value' => $this->getToWord((float)$this->fel_invoice->otrosDatos['valorFobFronteraBs'], 2, 'Bolivianos'), 'label' => 'Valor FOB Frontera Literal'];
            
            
            $data['$fel.product_rows'] = ['value' => $this->makeRowsProductExportacionMinerales(), 'label' => 'Detalle Productos'];
            
            $data['$fel.fleteInternoUSD'] = ['value' => !empty($this->fel_invoice->otrosDatos['fleteInternoUSD']) ? 
                                            '<tr>
                                                <th class="left-align" colspan="7">FLETE INTERNO USD</th>
                                                <td class="b-solid right-align">'. number_format((float)$this->fel_invoice->otrosDatos['fleteInternoUSD'] ,2,',','.').'</td>
                                            </tr>
                                            <tr>
                                                <td class="b-solid" colspan="8">SON: '.$this->getToWord((float)$this->fel_invoice->otrosDatos['fleteInternoUSD'], 2, 'Dólares Americanos').' </td>
                                            </tr>' : '', 
                                            'label' => 'Valor FOB Frontera Literal'];
            $data['$fel.valor_plata'] = ['value' => isset($this->fel_invoice->otrosDatos['valorPlata']) ? 
                                            '<tr>
                                                <th class="left-align" colspan="7">VALOR PLANTA</th>
                                                <td class="b-solid right-align">'. number_format((float) $this->fel_invoice->otrosDatos['valorPlata'] ,2,',','.').'</td>
                                            </tr>
                                            <tr>
                                                <td class="b-solid" colspan="8">SON: '.$this->getToWord((float)$this->fel_invoice->otrosDatos['valorPlata'], 2, 'Dólares Americanos').' </td>
                                            </tr>' : '', 
                                            'label' => 'Valor FOB Frontera Literal'];
            $data['$fel.subtotals'] = ['value' => $this->MakeSubtotalsRows(), 'label' => 'Subtotales'];
        return $data;
        
    }

    public function checkProperties($value){
        
        if(is_null($this->fel_invoice)){
            return;
        }
        
        if($value == '$fel.ruex' || $value == '$fel.nim'){
           return $this->fel_invoice->type_document_sector_id == 20 ? false : true; 
        }

        return false;

    }

    public function appendFieldVentaMinerales( $data ){
            $data['$fel.invoice_title'] = ['value' => $this->cuf ? 'FACTURA EXPORTACIÓN' : 'PREFACTURA EXPORTACIÓN', 'label' => 'Titulo'];
            $data['$fel.invoice_type'] = ['value' => $this->cuf ? 'Factura con derecho a Crédito Fiscal' : '', 'label' => 'Tipo de Factura'];
            $data['$fel.direccion_comprador'] = ['value' => $this->fel_invoice->direccionComprador, 'label' => 'Dirección Comprador'];
            $data['$fel.concentrado_granel'] = ['value' => $this->fel_invoice->concentradoGranel, 'label' => 'Concentrado Granel'];
            $data['$fel.puerto_transito'] = ['value' => $this->fel_invoice->puertoTransito, 'label' => 'Puerto Transito'];
            $data['$fel.puerto_destino'] = ['value' => $this->fel_invoice->puertoDestino, 'label' => 'Puerto Destino'];
            $data['$fel.origen'] = ['value' => $this->fel_invoice->origen, 'label' => 'Origen'];
            $data['$fel.incoterm'] = ['value' => $this->fel_invoice->incoterm, 'label' => 'INCOTERM'];
            $data['$fel.pais_destino'] = ['value' => Country::getDescriptionCountry($this->fel_invoice->paisDestino), 'label' => 'País Destino'];
            $data['$fel.moneda_transaccion'] = ['value' => Currency::getCurrecyDescription($this->fel_invoice->codigoMoneda), 'label' => 'Moneda Transacción'];
            $data['$fel.moneda_code'] = ['value' => Currencies::getShortCode($this->fel_invoice->codigoMoneda), 'label' => 'Código Moneda'];
            $data['$fel.tipo_cambio'] = ['value' => number_format((float)$this->fel_invoice->tipoCambio,5,',','.'), 'label' => 'Tipo Cambio Oficial'];
            $data['$fel.tipo_cambioANB'] = ['value' => number_format((float)$this->fel_invoice->tipoCambioANB,5,',','.'), 'label' => 'Tipo Cambio ANB'];
            $data['$fel.numero_lote'] = ['value' => $this->fel_invoice->numeroLote, 'label' => 'Número Lote'];
            $data['$fel.kilos_netosHumedos'] = ['value' => number_format((float)$this->fel_invoice->kilosNetosHumedos,2,',','.'), 'label' => 'Kilos Netos Húmedos'];
            $data['$fel.humedad_porcentaje'] = ['value' => (int) $this->fel_invoice->humedadPorcentaje, 'label' => 'Humedad Porcentaje'];
            $data['$fel.humedad_valor'] = ['value' => number_format((float)$this->fel_invoice->humedadValor,2,',','.'), 'label' => 'Humedad Valor'];
            $data['$fel.merma_porcentaje'] = ['value' => (int) $this->fel_invoice->mermaPorcentaje, 'label' => 'Merma Porcentaje'];
            $data['$fel.merma_valor'] = ['value' => number_format((float)$this->fel_invoice->mermaValor,2,',','.'), 'label' => 'Merma Valor'];
            $data['$fel.kilos_netosSecos'] = ['value' => number_format((float)$this->fel_invoice->kilosNetosSecos,2,',','.'), 'label' => 'Kilos Netos Secos'];
            $data['$fel.gastos_realizacion'] = ['value' => number_format((float)$this->fel_invoice->gastosRealizacion,2,',','.'), 'label' => 'Gastos Realización'];
            $data['$fel.iva'] = ['value' => number_format((float)$this->fel_invoice->iva,2,',','.') , 'label' => 'IVA'];
            $data['$fel.liquidacion_preliminar'] = ['value' => number_format((float)$this->fel_invoice->liquidacion_preliminar,2,',','.') , 'label' => 'Liquidación Preliminar'];
            $data['$fel.precio_concentrado'] = ['value' => number_format((float)$this->fel_invoice->montoTotalMoneda,2,',','.'), 'label' => 'Precio Concentrado'];
            $data['$fel.precio_ConcentradoLiteral'] = ['value' => $this->getToWord((float)$this->fel_invoice->montoTotalMoneda, 2, 'Dólares Americanos'), 'label' => 'Precion Concentrado Literal'];
            $data['$fel.total_literal'] = ['value' => 'SON: '. $this->getToWord($this->fel_invoice->montoTotal, 2, 'Bolivianos'), 'label' => 'Total Literal'];
            $data['$fel.monto_total'] = ['value' => number_format($this->fel_invoice->montoTotal,2,',','.'), 'label' => 'Monto Total'];
            
            $data['$fel.subtotal'] = ['value' => number_format((float)$this->fel_invoice->detalles[0]['subTotal'] ?? 0 + (float)$this->fel_invoice->detalles[1]['subTotal'] ?? 0 + (float)$this->fel_invoice->detalles[2]['subTotal'] ?? 0,2,',','.'), 'label' => 'Sub - Total'];

            $data['$fel.product_rows'] = ['value' => $this->makeRowsProductExportacionMinerales(), 'label' => 'Detalle Productos'];

            return $data;
    }


    public function getDocumentHtmlDesign($document_code, $data){


        switch ($document_code) {
            case TypeDocumentSector::EXPORTACION_MINERALES:
                return $this->appendFieldExportacionMinerales($data);
                break;
            case TypeDocumentSector::VENTA_INTERNA_MINERALES:
                return $this->appendFieldVentaMinerales($data);
                break;
            
            default:
                return $data;
                break;
            
        }

    }

}