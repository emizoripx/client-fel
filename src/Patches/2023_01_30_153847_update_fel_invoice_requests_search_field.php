<?php


class UpdateFelInvoiceRequestsSearchField
{
    /**
     * Run the patch.
     *
     * @return void
     */
    public function run()
    {
        
        \DB::statement(\DB::raw('update fel_invoice_requests left join invoices on invoices.id = fel_invoice_requests.id_origin left join clients on clients.id = invoices.client_id left join group_settings on group_settings.id = clients.group_settings_id set search_fields = substring(concat(if(fechaEmision!="", date_format(fechaEmision,"%Y-%m-%d"),"")," ",nombreRazonSocial," ",numeroDocumento, " ",codigoCliente, " ", ifnull(group_settings.name,"")," ",if(ifnull(nombreRazonSocial,"")=clients.name,"",ifnull(clients.name,"")) ) ,1,190) where fel_invoice_requests.id >0;'));
        
    }
}
