<?php

namespace EmizorIpx\ClientFel\Traits;

use App\Utils\Number;
use EmizorIpx\ClientFel\Models\FelInvoiceRequest;
use EmizorIpx\ClientFel\Models\Parametric\Country;
use EmizorIpx\ClientFel\Models\Parametric\Currency;
use EmizorIpx\ClientFel\Models\Parametric\IdentityDocumentType;
use EmizorIpx\ClientFel\Models\Parametric\Unit;
use EmizorIpx\ClientFel\Utils\Currencies;
use EmizorIpx\ClientFel\Utils\TypeDocumentSector;
use EmizorIpx\PrepagoBags\Utils\ModalityInvoicing;
use Exception;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

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
                <td style="padding-top: 10px; padding-bottom:10px;" class="b-solid center">'.
                (isset($felInvoice->detalles[0]['cantidadExtraccion']) ? number_format((float) bcdiv($felInvoice->detalles[0]['cantidadExtraccion'] ?? 0,'1',5),5,',','.').' '. Unit::getUnitDescription($felInvoice->detalles[0]['unidadMedidaExtraccion']) : null). '<br> ' .
                (isset($felInvoice->detalles[0]['cantidad']) ? number_format((float)$felInvoice->detalles[0]['cantidad'] ?? 0,5,',','.').' '.Unit::getUnitDescription($felInvoice->detalles[0]['unidadMedida']) : null).'</td>
                <td style="padding-top: 10px; padding-bottom:10px;" class="b-solid center">'.(isset($felInvoice->detalles[0]['precioUnitario']) ? number_format((float)$felInvoice->detalles[0]['precioUnitario'] ?? 0,5,',','.').'  '.'x '.Unit::getUnitDescription($felInvoice->detalles[0]['unidadMedida']) : null ).'</td>
                
            </tr>
            <tr>
                <td style="padding-top: 10px; padding-bottom:10px;" class="b-solid-left center bold">MERMA </td>
                <td style="padding-top: 10px; padding-bottom:10px;" class="b-solid-right center">'.number_format((float)$felInvoice->mermaPorcentaje,2,',','.').' % </td>
                <td style="padding-top: 10px; padding-bottom:10px;" class="b-solid center">'.number_format((float)$this->fel_invoice->mermaValor,2,',','.').' </td>
                <td style="padding-top: 10px; padding-bottom:10px;" class="b-solid center">'.(isset($felInvoice->detalles[1]['codigoProducto']) ? ($felInvoice->detalles[1]['codigoProducto']) : '') .' </td>
                <td style="padding-top: 10px; padding-bottom:10px;" class="b-solid center">'.(isset($felInvoice->detalles[1]['descripcionLeyes']) ? $felInvoice->detalles[1]['descripcionLeyes'] : null) .' </td>
                <td style="padding-top: 10px; padding-bottom:10px;" class="b-solid center">'.(isset($felInvoice->detalles[1]['codigoNandina']) ? $felInvoice->detalles[1]['codigoNandina'] : null) .'</td>
                <td style="padding-top: 10px; padding-bottom:10px;" class="b-solid center">'.
                (isset($felInvoice->detalles[1]['cantidadExtraccion']) ? number_format((float) bcdiv($felInvoice->detalles[1]['cantidadExtraccion'] ?? 0,'1',5),5,',','.').' '.Unit::getUnitDescription($felInvoice->detalles[1]['unidadMedidaExtraccion']) : null). '<br> ' .
                (isset($felInvoice->detalles[1]['cantidad']) ? number_format((float)$felInvoice->detalles[1]['cantidad'] ?? 0,5,',','.').' '.Unit::getUnitDescription($felInvoice->detalles[1]['unidadMedida']) : null).'</td>
                <td style="padding-top: 10px; padding-bottom:10px;" class="b-solid center">'.(isset($felInvoice->detalles[1]['precioUnitario']) ? number_format((float)$felInvoice->detalles[1]['precioUnitario'] ?? 0,5,',','.').'  '. 'x '.Unit::getUnitDescription($felInvoice->detalles[1]['unidadMedida']) : null ).'</td>
                
            </tr>
            <tr>
                <td style="padding-top: 10px; padding-bottom:10px;" class="b-solid-left center bold">KILOS NETOS SECOS </td>
                <td style="padding-top: 10px; padding-bottom:10px;" class="b-solid-right center"> </td>
                <td style="padding-top: 10px; padding-bottom:10px;" class="b-solid center">'.number_format((float)$this->fel_invoice->kilosNetosSecos,2,',','.').' </td>
                <td style="padding-top: 10px; padding-bottom:10px;" class="b-solid center">'. (isset($felInvoice->detalles[2]['codigoProducto']) ? ($felInvoice->detalles[2]['codigoProducto']) : '') .' </td>
                <td style="padding-top: 10px; padding-bottom:10px;" class="b-solid center">'. (isset($felInvoice->detalles[2]['descripcionLeyes']) ? $felInvoice->detalles[2]['descripcionLeyes'] : null) .' </td>
                <td style="padding-top: 10px; padding-bottom:10px;" class="b-solid center">'. (isset($felInvoice->detalles[2]['codigoNandina']) ? $felInvoice->detalles[2]['codigoNandina'] : null) .'</td>
                <td style="padding-top: 10px; padding-bottom:10px;" class="b-solid center">'. 
                (isset($felInvoice->detalles[2]['cantidadExtraccion']) ? number_format((float) bcdiv($felInvoice->detalles[2]['cantidadExtraccion'] ?? 0,'1',5),5,',','.').' '.Unit::getUnitDescription($felInvoice->detalles[2]['unidadMedidaExtraccion']) : null). '<br> ' .
                (isset($felInvoice->detalles[2]['cantidad']) ? number_format((float)$felInvoice->detalles[2]['cantidad'] ?? 0,5,',','.').' '.Unit::getUnitDescription($felInvoice->detalles[2]['unidadMedida']) : null).'</td>
                <td style="padding-top: 10px; padding-bottom:10px;" class="b-solid center">'.  (isset($felInvoice->detalles[2]['precioUnitario']) ? number_format((float)$felInvoice->detalles[2]['precioUnitario'] ?? 0,5,',','.').'  '. 'x '.Unit::getUnitDescription($felInvoice->detalles[2]['unidadMedida']) : null ).'</td>
                
            </tr>
            ';

        return $rows_table;
    }

    public function makeRowsProductComercialExportacion(){

        $rows_table = '';
        $felInvoice = $this->fel_invoice;
        $subtotal = 0;
        $rows_table .= '<table id="product-table">
                            <thead>
                            <th width="15%">NANDINA</th>
                            <th width="10%">CANTIDAD (Quantity)</th>
                            <th width="30%">DESCRIPCION <br> (Description)</th>
                            <th width="15%">UNIDAD MEDIDA (Unit of Measurement)</th>
                            <th width="13%">PRECIO UNITARIO (Unit Value)</th>
                            <th width="17%">SUBTOTAL</th>
                            </thead><tbody>';
        foreach($felInvoice->detalles as $detalle){
            $rows_table .= '
            <tr>
                <td>'. $detalle['codigoNandina'] .'</td>
                <td>'. number_format((float) $detalle['cantidad'],5,',','.') .'</td>
                <td>'. $detalle['descripcion'] .'</td>
                <td>'. Unit::getUnitDescription($detalle['unidadMedida']) .'</td>
                <td class="right-align">'.number_format((float)$detalle['precioUnitario'],5,',','.').' </td>
                <td class="right-align">'. number_format((float) $detalle['subTotal'],5,',','.') .'</td>
            </tr>
            ';
            $subtotal += floatval($detalle['subTotal']); 
        }
        $rows_table .= '
        <tr>
            <td colspan=5 style="text-align: right;"> <b> TOTAL DETALLE ('.Currency::getCurrecyDescription($this->fel_invoice->codigoMoneda).') <br> (Total Detail) </b></td>
            <td> '. number_format((float) $subtotal,2,',','.').'</td>
        </tr>
        <tr>
            <td colspan=5 style="text-align: right;"> INCOTERM y alcance del Total detalle de la transacción (INCOTERM and scope of the Total Transaction Details)</td>
            <td> '. $felInvoice->incoterm_detalle .'</td>
        </tr>

        </tbody>
        </table>';
        
        return $rows_table;

    }

    public function MakeSubtotalsRows(){
        $rows = (isset($this->fel_invoice->detalles[0]['descripcion']) ? 
        '<tr>
            <th class="left-align" colspan="7">'.strtoupper($this->fel_invoice->detalles[0]['descripcion']).'</th>
            <td class="b-solid right-align">'. number_format((float) bcdiv($this->fel_invoice->detalles[0]['subTotal'] ,'1',5),5,',','.') .'</td>
        </tr>' : '')
        
        .(isset($this->fel_invoice->detalles[1]['descripcion']) ? '
        <tr>
            <th class="left-align" colspan="7">'.strtoupper($this->fel_invoice->detalles[1]['descripcion']).'</th>
            <td class="b-solid right-align">'. number_format((float) bcdiv($this->fel_invoice->detalles[1]['subTotal'] ,'1',5),5,',','.') .'</td>
        </tr>' : '')
        . (isset($this->fel_invoice->detalles[2]['descripcion']) ? '
        <tr>
            <th class="left-align" colspan="7">'.strtoupper($this->fel_invoice->detalles[2]['descripcion']).'</th>
            <td class="b-solid right-align">'. number_format((float) bcdiv($this->fel_invoice->detalles[2]['subTotal'] ,'1',5),5,',','.') .'</td>
        </tr>' : ''
        );


        return $rows;
    }

    public function appendFieldExportacionMinerales($data){


            $data['$fel.invoice_title'] = ['value' => $this->cuf ? 'FACTURA EXPORTACIÓN' : 'PREFACTURA EXPORTACIÓN', 'label' => 'Titulo'];
            $data['$fel.invoice_type'] = ['value' => $this->cuf ? '('.$this->fel_invoice->type_invoice.')' : '', 'label' => 'Tipo de Factura'];
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
            $data['$fel.moneda_code'] = ['value' => Currency::getCurrecyDescription($this->fel_invoice->codigoMoneda), 'label' => 'Código Moneda'];
            $data['$fel.tipo_cambio'] = ['value' => number_format((float)$this->fel_invoice->tipoCambio,2,',','.'), 'label' => 'Tipo Cambio Oficial'];
            $data['$fel.tipo_cambioANB'] = ['value' => number_format((float)$this->fel_invoice->tipoCambioANB,2,',','.'), 'label' => 'Tipo Cambio ANB'];
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
            $data['$fel.valor_FOBFronteraLiteral'] = ['value' => $this->getToWord((float)$this->fel_invoice->otrosDatos['valorFobFrontera'], 2, '('.Currency::getCurrecyDescription($this->fel_invoice->codigoMoneda).')'), 'label' => 'Valor FOB Frontera Literal'];
            $data['$fel.valor_FOBFronteraBsLiteral'] = ['value' => $this->getToWord((float)$this->fel_invoice->otrosDatos['valorFobFronteraBs'], 2, '(Bolivianos)'), 'label' => 'Valor FOB Frontera Literal'];
            
            $data['$monto_total'] = ['value' => number_format(collect($this->fel_invoice->detalles)->sum('subTotal'),2,',','.'), 'label' => 'Monto Total'];
            $data['$total_literal'] = ['value' => 'SON: '. $this->getToWord(collect($this->fel_invoice->detalles)->sum('subTotal'), 2, '('.Currency::getCurrecyDescription($this->fel_invoice->codigoMoneda).')'), 'label' => 'Total Literal'];
            
            
            $data['$fel.product_rows'] = ['value' => $this->makeRowsProductExportacionMinerales(), 'label' => 'Detalle Productos'];
            
            $data['$fel.fleteInternoUSD'] = ['value' => !empty($this->fel_invoice->otrosDatos['fleteInternoUSD']) ? 
                                            '<tr>
                                                <th class="left-align" colspan="7">FLETE INTERNO USD</th>
                                                <td class="b-solid right-align">'. number_format((float)$this->fel_invoice->otrosDatos['fleteInternoUSD'] ,2,',','.').'</td>
                                            </tr>
                                            <tr>
                                                <td class="b-solid" colspan="8">SON: '.$this->getToWord((float)$this->fel_invoice->otrosDatos['fleteInternoUSD'], 2, '('.Currency::getCurrecyDescription($this->fel_invoice->codigoMoneda).')').' </td>
                                            </tr>' : '', 
                                            'label' => 'Valor FOB Frontera Literal'];
            // $data['$fel.valor_plata'] = ['value' => isset($this->fel_invoice->otrosDatos['valorPlata']) ? 
            //                                 '<tr>
            //                                     <th class="left-align" colspan="7">VALOR PLANTA</th>
            //                                     <td class="b-solid right-align">'. number_format((float) $this->fel_invoice->otrosDatos['valorPlata'] ,2,',','.').'</td>
            //                                 </tr>
            //                                 <tr>
            //                                     <td class="b-solid" colspan="8">SON: '.$this->getToWord((float)$this->fel_invoice->otrosDatos['valorPlata'], 2, 'Dólares Americanos').' </td>
            //                                 </tr>' : '', 
            //                                 'label' => 'Valor FOB Frontera Literal'];
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
            $data['$fel.invoice_title'] = ['value' => $this->cuf ? 'FACTURA VENTA INTERNA MINERALES' : 'PREFACTURA VENTA INTERNA MINERALES', 'label' => 'Titulo'];
            $data['$fel.invoice_type'] = ['value' => $this->cuf ? '('.$this->fel_invoice->type_invoice.')' : '', 'label' => 'Tipo de Factura'];
            $data['$fel.direccion_comprador'] = ['value' => $this->fel_invoice->direccionComprador, 'label' => 'Dirección Comprador'];
            $data['$fel.concentrado_granel'] = ['value' => $this->fel_invoice->concentradoGranel, 'label' => 'Concentrado Granel'];
            $data['$fel.puerto_transito'] = ['value' => $this->fel_invoice->puertoTransito, 'label' => 'Puerto Transito'];
            $data['$fel.puerto_destino'] = ['value' => $this->fel_invoice->puertoDestino, 'label' => 'Puerto Destino'];
            $data['$fel.origen'] = ['value' => $this->fel_invoice->origen, 'label' => 'Origen'];
            $data['$fel.incoterm'] = ['value' => $this->fel_invoice->incoterm, 'label' => 'INCOTERM'];
            $data['$fel.pais_destino'] = ['value' => Country::getDescriptionCountry($this->fel_invoice->paisDestino), 'label' => 'País Destino'];
            $data['$fel.moneda_transaccion'] = ['value' => Currency::getCurrecyDescription($this->fel_invoice->codigoMoneda), 'label' => 'Moneda Transacción'];
            $data['$fel.moneda_code'] = ['value' => Currency::getCurrecyDescription($this->fel_invoice->codigoMoneda), 'label' => 'Código Moneda'];
            $data['$fel.tipo_cambio'] = ['value' => number_format((float)$this->fel_invoice->tipoCambio,2,',','.'), 'label' => 'Tipo Cambio Oficial'];
            $data['$fel.tipo_cambioANB'] = ['value' => number_format((float)$this->fel_invoice->tipoCambioANB,2,',','.'), 'label' => 'Tipo Cambio ANB'];
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
            $data['$fel.precio_ConcentradoLiteral'] = ['value' => $this->getToWord((float)$this->fel_invoice->montoTotalMoneda, 2, '('.Currency::getCurrecyDescription($this->fel_invoice->codigoMoneda).')'), 'label' => 'Precion Concentrado Literal'];
            $data['$fel.total_literal'] = ['value' => 'SON: '. $this->getToWord($this->fel_invoice->montoTotal, 2, '(Bolivianos)'), 'label' => 'Total Literal'];
            $data['$fel.monto_total'] = ['value' => number_format($this->fel_invoice->montoTotal,2,',','.'), 'label' => 'Monto Total'];
            
            $data['$fel.subtotal'] = ['value' => number_format((float)collect($this->fel_invoice->detalles)->sum('subTotal'),2,',','.'), 'label' => 'Sub - Total'];

            $data['$fel.product_rows'] = ['value' => $this->makeRowsProductExportacionMinerales(), 'label' => 'Detalle Productos'];
            $data['$fel.subtotals'] = ['value' => $this->MakeSubtotalsRows(), 'label' => 'Subtotales'];

            return $data;
    }

    public function makeClientDetail(){
        $clientDetails = '
            <table id="client-details">
                <tbody>

                    <tr>
                        <th colspan=4 style="font-size:36px;">FACTURA COMERCIAL EXPORTACIÓN <br> (COMERCIAL INVOICE) </th>
                    </tr>
                    
                    <tr>
                        <td colspan=4 style="font-size:13px; text-align:center; padding-bottom: 3rem;"> <span style="font-size:12pt;"> <b>'. ModalityInvoicing::getModalityInvoicing($this->company->company_detail->modality_code) .'</b> </span> <br>('. $this->fel_invoice->type_invoice .')</t>
                    </tr>
                    <tr>
                        <td><b>Fecha (Date):</b></td>
                        <td>'. date("d/m/Y H:i:s", strtotime($this->fel_invoice->fechaEmision)).'</td>
                        <td><b>'. explode('-', IdentityDocumentType::getDocumentTypeDescription($this->fel_invoice->codigoTipoDocumentoIdentidad))[0] .':</b></td>
                        <td>'. $this->fel_invoice->numeroDocumento .'</td>
                    </tr>
                    <tr>
                        <td><b>Nombre/Razón Social (Name Buyer):</b></td>
                        <td>'. $this->fel_invoice->nombreRazonSocial .'</td>
                        <td><b>Dirección Comprador (Address):</b></td>
                        <td>'. $this->fel_invoice->direccionComprador .'</td>
                    </tr>
                    <tr>
                        <td><b>INCOTERM:</b></td>
                        <td>'. $this->fel_invoice->incoterm .'</td>
                        <td><b>Puerto Destino (Destination Port):</b></td>
                        <td>'. $this->fel_invoice->puertoDestino .'</td>
                    </tr>
                    <tr>
                        <td><b>Moneda de la Transacción Comercial (Comercial Transaction Currency):</b></td>
                        <td>'. Currency::getCurrecyDescription($this->fel_invoice->codigoMoneda) .'</td>
                        <td><b>Tipo de Cambio (Exchange Date):</b></td>
                        <td>'. number_format((float)$this->fel_invoice->tipoCambio,2,',','.') .'</td>
                    </tr>
                </tbody>
            </table>
        ';

        return $clientDetails;
    }

    public function appendFieldComercialExportacion($data){

            $data['$fel.invoice_title'] = ['value' => $this->cuf ? 'FACTURA COMERCIAL EXPORTACIÓN' : 'PREFACTURA COMERCIAL EXPORTACIÓN', 'label' => 'Titulo'];
            $data['$fel.invoice_type'] = ['value' => $this->cuf ? '('.$this->fel_invoice->type_invoice.')' : '', 'label' => 'Tipo de Factura'];
            $data['$fel.moneda_code'] = ['value' => Currencies::getShortCode($this->fel_invoice->codigoMoneda), 'label' => 'Código Moneda'];
            $data['$fel.tipo_cambio'] = ['value' => number_format((float)$this->fel_invoice->tipoCambio,2,',','.'), 'label' => 'Tipo Cambio Oficial'];
            $data['$fel.gastos_realizacion'] = ['value' => number_format((float)$this->fel_invoice->gastosRealizacion,2,',','.'), 'label' => 'Gastos Realización'];
            $data['$fel.total_literal'] = ['value' => 'SON: '. $this->getToWord($this->fel_invoice->montoTotal, 2, 'Bolivianos'), 'label' => 'Total Literal'];
            $data['$fel.monto_total'] = ['value' => number_format($this->fel_invoice->montoTotal,2,',','.'), 'label' => 'Monto Total'];
            
            $data['$fel.products_rows'] = [ 'value' => $this->makeRowsProductComercialExportacion(), 'label' => 'Detalles Productos' ];
            $data['$fel.client_details'] = [ 'value' => $this->makeClientDetail(), 'label' => 'Detalles Cliente' ];
            
            $data['$fel.costos_GastosNacionales'] = [ 'value' => 
            '<table id="gastos-nacionales">
                <tbody>
                    <tr>
                        <td>Gasto Transporte</td>
                        <td style="text-align: right;">'. (isset($this->fel_invoice->costosGastosNacionales) ?  number_format((float)$this->fel_invoice->costosGastosNacionales['gastoTransporte'],2,',','.') : 0) .'</td>
                    </tr>
                    <tr>
                        <td>Gasto de Seguro</td>
                        <td style="text-align: right;">'. (isset($this->fel_invoice->costosGastosNacionales) ? number_format((float) $this->fel_invoice->costosGastosNacionales['gastoSeguro'],2,',','.') : 0) .'</td>
                    </tr>
                    <tr>
                        <td><b>SUBTOTAL FOB</b></td>
                        <td style="text-align: right;">'. (isset($this->fel_invoice->totalGastosNacionalesFob) ? number_format((float)$this->fel_invoice->totalGastosNacionalesFob,2,',','.') : 0) .'</td>
                    </tr>
                </tbody>
            </table>

            '
            , 'label' => 'Detalles Productos' ];

            $data['$fel.costos_GastosInternacionales'] = [ 'value' => 
            '<table id="gastos-internacionales">
                <tbody>
                    <tr>
                        <td>Gasto Transporte</td>
                        <td style="text-align: right;">'. (isset($this->fel_invoice->costosGastosInternacionales) ? number_format((float)$this->fel_invoice->costosGastosInternacionales['gastoTransporte'],2,',','.') : 0) .'</td>
                    </tr>
                    <tr>
                        <td>Gasto de Seguro</td>
                        <td style="text-align: right;">'. (isset($this->fel_invoice->costosGastosInternacionales) ? number_format((float)$this->fel_invoice->costosGastosInternacionales['gastoSeguro'],2,',','.') : 0) .'</td>
                    </tr>
                    <tr>
                        <td><b>SUBTOTAL CIF</b></td>
                        <td style="text-align: right;">'. (isset($this->fel_invoice->costosGastosInternacionales) ? number_format((float)$this->fel_invoice->totalGastosInternacionales,2,',','.') : 0) .'</td>
                    </tr>
                </tbody>
            </table>

            '
            , 'label' => 'Detalles Productos' ];

            $data['$fel.totales'] = [ 'value' => 
            '<table id="totales">
                <tbody>
                    <tr>
                        <td>Son: '.$this->getToWord($this->fel_invoice->montoTotalMoneda, 2, 'Dólares').'</td>
                        <td style="text-align: right;"><b>Total General (Dolar)</b></td>
                        <td style="text-align: right;">'. number_format((float)$this->fel_invoice->montoTotalMoneda,2,',','.') .'</td>
                    </tr>
                    <tr>
                        <td>Son: '.$this->getToWord($this->fel_invoice->montoTotal, 2, 'Bolivianos').'</td>
                        <td style="text-align: right;"><b>Total General (Bolivianos)</b></td>
                        <td style="text-align: right;">'. number_format((float)$this->fel_invoice->montoTotal,2,',','.') .'</td>
                    </tr>
                </tbody>
            </table>

            '
            , 'label' => 'Detalles Productos' ];
            $data['$fel.bultos'] = [ 'value' => 
            '<table id="bultos">
                <tbody>
                    <tr>
                        <td><b>Número y Descripción de Paquetes (Bultos) <br> (Number and Description of Boxes)</b></td>
                    </tr>
                    <tr>
                        <td>'. $this->fel_invoice->numeroDescripcionPaquetesBultos.'</td>
                    </tr>
                    <tr>
                        <td><b>Información Adicional <br> (Additional Information)</b></td>
                    </tr>
                    <tr>
                        <td>'. $this->fel_invoice->informacionAdicional .'</td>
                    </tr>
                </tbody>
            </table>

            '
            , 'label' => 'Detalles Productos' ];

            return $data;

    }

    public function makeClientDetailCreditoDebito( $factura ){
        $clientDetails = '<table id="client-details">
                            <tbody>

                                <tr>
                                    <th colspan=4 style="font-size:38px; padding-bottom: 30px;"> NOTA CRÉDITO - DÉBITO</th>
                                </tr>
                                
                                <tr>
                                    <td><b>Fecha:</b></td>
                                    <td>'. date("d/m/Y H:i:s", strtotime($this->fel_invoice->fechaEmision)).'</td>
                                    <td><b>'. explode('-', IdentityDocumentType::getDocumentTypeDescription($this->fel_invoice->codigoTipoDocumentoIdentidad))[0] .'</td>
                                    <td>'. $factura->numeroDocumento .'</td>
                                </tr>
                                <tr>
                                    <td><b>Nombre/Razón Social:</b></td>
                                    <td>'. $factura->nombreRazonSocial .'</td>
                                    <td><b>Fecha Factura:</b></td>
                                    <td>'. date("d/m/Y H:i:s", strtotime($factura->fechaEmision))  .'</td>
                                </tr>
                                <tr>
                                    <td><b>Nº Factura:</b></td>
                                    <td>'. $factura->numeroFactura .'</td>
                                    <td><b>Nº Autorización/CUF:</b></td>
                                    <td>'. $factura->cuf .'</td>
                                </tr>
                                
                            </tbody>
                        </table>';
        return $clientDetails;
    }

    public function makeRowsProductFacturaOriginal( $detalles, $codigoMoneda ,$flag ){
        
        $subtotal = 0;
        $rows_table = '<table id="product-table">
                        <thead>
                        <th width="15%">Código Producto</th>
                        <th width="10%">Cantidad</th>
                        <th width="30%">Descripción</th>
                        <th width="20%">Precio Unitario</th>
                        <th width="10%">Descuento</th>
                        <th width="15%">Subtotal</th>
                        </thead><tbody>';

        foreach ($detalles as $detalle) {
            $rows_table .= '
                <tr>
                    <td>'. $detalle['codigoProducto'] .'</td>
                    <td style="text-align: right;">'. $detalle['cantidad'] .'</td>
                    <td>'. $detalle['descripcion'] .'</td>
                    <td style="text-align: right;">'. number_format((float)$detalle['precioUnitario'],2,',','.') .'</td>
                    <td style="text-align: right;">'. (array_key_exists('descuento', $detalle) ? number_format((float)$detalle['descuento'],2,',','.') : '0,00' ) .' </td>
                    <td class="right-align">'. number_format((float)$detalle['subTotal'],2,',','.') .'</td>
                </tr>
            ';
            $subtotal += intval($detalle['subTotal']); 
        }


        if($flag){
            $rows_table .= '
                    <tr>
                        <td rowspan=2 colspan=3 style="text-align: left; vertical-align: top; border-bottom: 0px solid #e6e6e6;"> SON: '. $this->getToWord($this->fel_invoice->montoTotal, 2, Currencies::getDescriptionCurrency($codigoMoneda)) .'</td>
                        <td colspan=2 style="text-align: right;"> <b> Monto Total Devuelto '. Currencies::getShortCode($codigoMoneda).' </b></td>
                        <td> '. number_format((float) $this->fel_invoice->montoTotal,2,',','.') .'</td>
                    </tr>
                    
                    <tr>
                        <td colspan=2 style="text-align: right;"> <b>Monto Efectivo de Débito-Crédito</b></td>
                        <td> '. number_format((float) $this->fel_invoice->montoEfectivoCreditoDebito,2,',','.') .'</td>
                    </tr>
                    
                    </tbody>
                    </table>';

        } else{
            $rows_table .= '
                    <tr>
                        <td colspan=5 style="text-align: right;"> <b> Monto Total Original '. Currencies::getShortCode($codigoMoneda).' </b></td>
                        <td> '. number_format((float)$subtotal,2,',','.') .'</td>
                    </tr>
                    
                    </tbody>
                    </table>';
        }

        return $rows_table;

    }

    public function appendFieldCreditoDebito( $data ){
        $facturaOriginal = $this->fel_invoice->getFacturaOrigin();

        $data['$fel.client_details'] = [ 'value' => $this->makeClientDetailCreditoDebito($facturaOriginal), 'label' => 'Detalles Cliente Debito' ];

        $data['$fel.products_rows_original'] = [ 'value' => $this->makeRowsProductFacturaOriginal($facturaOriginal->detalles, $facturaOriginal->codigoMoneda ,false), 'label' => 'Detalles Productos' ];
        $data['$fel.products_rows_devolucion'] = [ 'value' => $this->makeRowsProductFacturaOriginal($this->fel_invoice->detalles, $this->fel_invoice->codigoMoneda, true), 'label' => 'Detalles Productos' ];

        $data['$fel.moneda_code'] = ['value' => Currencies::getShortCode($this->fel_invoice->codigoMoneda), 'label' => 'Código Moneda'];
        $data['$fel.tipo_cambio'] = ['value' => number_format((float)$this->fel_invoice->tipoCambio,2,',','.'), 'label' => 'Tipo Cambio Oficial'];
        $data['$fel.total_literal'] = ['value' => 'SON: '. $this->getToWord($this->fel_invoice->montoTotal, 2, 'Bolivianos'), 'label' => 'Total Literal'];
        $data['$fel.monto_total'] = ['value' => number_format($this->fel_invoice->montoTotal,2,',','.'), 'label' => 'Monto Total'];

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
            case TypeDocumentSector::COMERCIAL_EXPORTACION:
                return $this->appendFieldComercialExportacion($data);
                break;
            case TypeDocumentSector::DEBITO_CREDITO:
                return $this->appendFieldCreditoDebito($data);
                break;
            
            default:
                return $data;
                break;
            
        }

    }

    public function getPaymentQR(){
        $component = "";
        if(!$this->fel_invoice->cuf){
            try {
                $qr = $this->generateQR();
                \Log::debug("QR");

                $logoQR = public_path().'/images/qr-simple.jpg';

                \Log::debug("URL ". $logoQR);
                $component = '
                        <div id="box-qr">
                            <div style="font-size: 22px; text-align:center; padding: 5px; color:#3a3939;"><strong>Escanea y paga</strong> desde tu celular</div>
                            <img src="data:image/jpeg;base64,'.$qr->data->qrImage .'" alt="" title="" width="180" height="180" style="display: -webkit-inline-box; padding: 10px 10px; border: 1px solid gainsboro;" />
                            <div style="display:flex; padding-top: 5px; justify-content: center; align-items: center; width:100%;">
                                <span style="font-size:28px; font-weight: 600;">QR </span> &nbsp;&nbsp;
                                <img src="'.$logoQR.'" height="45"/>
                            </div>
                        </div>';
            } catch (Exception $ex) {
                \Log::debug("Error to get QR ". $ex->getMessage());
            }
        }

        return $component;
    }

}