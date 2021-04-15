<?php

namespace EmizorIpx\ClientFel\Traits;

use EmizorIpx\ClientFel\Models\Parametric\Country;
use EmizorIpx\ClientFel\Models\Parametric\Currency;
use EmizorIpx\ClientFel\Models\Parametric\Unit;
use EmizorIpx\ClientFel\Utils\Currencies;

trait HtmlDesignTrait{

    

    public function makeRowsProductExportacionMinerales(){
        $felInvoice = $this->fel_invoice;

        $rows_table = '';
        
        foreach($felInvoice->detalles as $line){
            
            $rows_table = $rows_table. '
            <tr>
                <td style="padding-top: 10px; padding-bottom:10px;" class="b-solid center">'.$line['codigoProducto'].' </td>
                <td style="padding-top: 10px; padding-bottom:10px;" class="b-solid center">'.$line['descripcionLeyes'].' </td>
                <td style="padding-top: 10px; padding-bottom:10px;" class="b-solid center">'.$line['codigoNandina'] .'</td>
                <td style="padding-top: 10px; padding-bottom:10px;" class="b-solid center">'.$line['cantidad'].' '.Unit::getUnitDescription($line['unidadMedida']).'</td>
                <td style="padding-top: 10px; padding-bottom:10px;" class="b-solid center">'.$line['precioUnitario'].'  '. Currencies::getShortCode($felInvoice->codigoMoneda).'/'.Unit::getUnitDescription($line['unidadMedida']).'</td>
                <td style="padding-top: 10px; padding-bottom:10px;" class="b-solid right-align">'.$line['subTotal'].' '. Currencies::getShortCode($felInvoice->codigoMoneda).'</td>

            </tr>';
        }

        return $rows_table;
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
            $data['$fel.tipo_cambio'] = ['value' => $this->fel_invoice->tipoCambio, 'label' => 'Tipo Cambio Oficial'];
            $data['$fel.tipo_cambioANB'] = ['value' => $this->fel_invoice->tipoCambioANB, 'label' => 'Tipo Cambio ANB'];
            $data['$fel.numero_lote'] = ['value' => $this->fel_invoice->numeroLote, 'label' => 'Número Lote'];
            $data['$fel.kilos_netosHumedos'] = ['value' => $this->fel_invoice->kilosNetosHumedos, 'label' => 'Kilos Netos Húmedos'];
            $data['$fel.humedad_porcentaje'] = ['value' => (int) $this->fel_invoice->humedadPorcentaje, 'label' => 'Humedad Porcentaje'];
            $data['$fel.humedad_valor'] = ['value' => $this->fel_invoice->humedadValor, 'label' => 'Humedad Valor'];
            $data['$fel.merma_porcentaje'] = ['value' => (int) $this->fel_invoice->mermaPorcentaje, 'label' => 'Merma Porcentaje'];
            $data['$fel.merma_valor'] = ['value' => $this->fel_invoice->mermaValor, 'label' => 'Merma Valor'];
            $data['$fel.kilos_netosSecos'] = ['value' => $this->fel_invoice->kilosNetosSecos, 'label' => 'Kilos Netos Secos'];
            $data['$fel.gastos_realizacion'] = ['value' => $this->fel_invoice->gastosRealizacion, 'label' => 'Gastos Realización'];
            $data['$fel.valor_FOBFrontera'] = ['value' => $this->fel_invoice->otrosDatos['valorFobFrontera'], 'label' => 'Valor FOB Frontera'];
            $data['$fel.valor_FOBFronteraBs'] = ['value' => $this->fel_invoice->otrosDatos['valorFobFronteraBs'], 'label' => 'Valor FOB Frontera'];
            $data['$fel.valor_FOBFronteraLiteral'] = ['value' => $this->getToWord((float)$this->fel_invoice->otrosDatos['valorFobFrontera'], 2, 'Dólares'), 'label' => 'Valor FOB Frontera Literal'];
            $data['$fel.valor_FOBFronteraBsLiteral'] = ['value' => $this->getToWord((float)$this->fel_invoice->otrosDatos['valorFobFronteraBs'], 2, 'Bolivianos'), 'label' => 'Valor FOB Frontera Literal'];
            
            
            $data['$fel.product_rows'] = ['value' => $this->makeRowsProductExportacionMinerales(), 'label' => 'Detalle Productos'];
            
            $data['$fel.fleteInternoUSD'] = ['value' => isset($this->fel_invoice->otrosDatos['fleteInternoUSD']) ? 
                                            '<tr>
                                                <th class="left-align" colspan="5">FLETE INTERNO USD</th>
                                                <td class="b-solid right-align">'. $this->fel_invoice->otrosDatos['fleteInternoUSD'] .'</td>
                                            </tr>
                                            <tr>
                                                <td class="b-solid" colspan="6">SON '.$this->getToWord((float)$this->fel_invoice->otrosDatos['fleteInternoUSD'], 2, 'Dolares').' </td>
                                            </tr>' : '', 
                                            'label' => 'Valor FOB Frontera Literal'];
            $data['$fel.valor_plata'] = ['value' => isset($this->fel_invoice->otrosDatos['valorPlata']) ? 
                                            '<tr>
                                                <th class="left-align" colspan="5">FLETE INTERNO USD</th>
                                                <td class="b-solid right-align">'. $this->fel_invoice->otrosDatos['valorPlata'] .'</td>
                                            </tr>
                                            <tr>
                                                <td class="b-solid" colspan="6">SON '.$this->getToWord((float)$this->fel_invoice->otrosDatos['valorPlata'], 2, 'Dolares').' </td>
                                            </tr>' : '', 
                                            'label' => 'Valor FOB Frontera Literal'];
            $data['$fel.partida_arancelaria'] = ['value' => isset($this->fel_invoice->otrosDatos['partidaArancelaria']) ? 
                                            '<tr>
                                                <th class="left-align" colspan="5">FLETE INTERNO USD</th>
                                                <td class="b-solid right-align">'. $this->fel_invoice->otrosDatos['partidaArancelaria'] .'</td>
                                            </tr>
                                            <tr>
                                                <td class="b-solid" colspan="6">SON '.$this->getToWord((float)$this->fel_invoice->otrosDatos['partidaArancelaria'], 2, 'Dolares').' </td>
                                            </tr>' : '', 
                                            'label' => 'Valor FOB Frontera Literal'];
        return $data;
        
    }

    public function checkProperties($value){
        
        if($value == '$fel.ruex' || $value == '$fel.nim'){
           return $this->fel_invoice->type_document_sector_id == 20 ? false : true; 
        }

        return false;

    }

}