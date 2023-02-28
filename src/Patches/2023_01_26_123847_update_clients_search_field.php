<?php


class UpdateClientsSearchField
{
    /**
     * Run the patch.
     *
     * @return void
     */
    public function run()
    {
        
        \DB::statement(\DB::raw("update fel_clients left join clients on fel_clients.id_origin = clients.id left join group_settings on group_settings.id = clients.group_settings_id set fel_clients.search_fields = substring(concat(ifnull(clients.name,''),' ', ifnull(clients.number,''),' ', if(ifnull(fel_clients.business_name,'')=clients.name,'',ifnull(fel_clients.business_name,'')) ,' ', ifnull(fel_clients.document_number,''),' ', ifnull(group_settings.name,'') ),1,249) where fel_clients.id > 0"));
        
    }
}
