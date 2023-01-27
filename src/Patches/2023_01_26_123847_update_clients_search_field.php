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
        
        \DB::statement(\DB::raw("update fel_clients left join clients on fel_clients.id_origin = clients.id set fel_clients.search_fields =concat(ifnull(clients.name,''),' ', ifnull(clients.id_number,''),' ', ifnull(fel_clients.business_name,''),' ', ifnull(fel_clients.document_number,'')) where fel_clients.id > 0;"));
        
    }
}
