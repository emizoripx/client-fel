<?php

use EmizorIpx\ClientFel\Models\FelInvoiceRequest;
use Carbon\Carbon;
class UpdateTemplateChangesS3
{
    /**
     * Run the patch.
     *
     * @return void
     */
    public function run()
    {

        $array_custom_docs = [
            1001665021 => [
                "carranza",
                [1]
            ],
            1004057020 => [
                "andean",
                [3]
            ],
            1005479029 => [
                "saite",
                [3]
            ],
            1015601025 => [
                "blacutt",
                [3]
            ],
            1015607026 => [
                "urkupina",
                [3]
            ],
            1017233026 => [
                "manquiri",
                [20]
            ],
            10204150212 => [
                "msc",
                [1, 3, 20, 21, 24]
            ],
            1028129026 => [
                "terbol",
                [1]
            ],
            1028301022 => [
                "megalabs",
                [1, 24]
            ],
            1028641023 => [
                "total_energies",
                [38]
            ],
            1028767020 => [
                "provivienda",
                [1]
            ],
            191310020 => [
                "crediseguros",
                [1, 2]
            ],
            301400028 => [
                "roque",
                [20]
            ],
            327206023 => [
                "talleres_villavicencio",
                [1]
            ],
            344946021 => [
                "sansilvestre",
                [20]
            ],
            373172026 => [
                "hurbens",
                [1]
            ],
            379466026 => [
                "vendis",
                [1]
            ],
            402044022 => [
                "delienvios",
                [1]
            ],
            421759027 => [
                "sanfrancisco",
                [20]
            ],
            422989029 => [
                "centro_movil",
                [1]
            ]
        ];
        \App\Models\Company::cursor()->each( function ($company) use($array_custom_docs) {
            $settings = $company->settings;
            $company->nit = intval($settings->id_number);
            \Log::debug("\n ID= $company->id COMPANY: " . $company->name ." NOMBRE =  ".$settings->name. " nit: " . $company->nit . " \n");

            if (isset($array_custom_docs[$company->nit])) {
                \Log::debug("TEMPLATES CUSTOM================================ \n", $array_custom_docs[$company->nit]);
                
                foreach ($array_custom_docs[$company->nit][1] as $document_sector) {
                    
                    $template = \DB::table('fel_templates')->whereCompanyId($company->id)->where('document_sector_code', $document_sector)->first();

                    if (empty($template)) {
                        \DB::table('fel_templates')->insert(['company_id'=> $company->id, "document_sector_code" => $document_sector, "blade_resource" => "templates/company/$company->nit/$document_sector/" . $array_custom_docs[$company->nit][0] . ".blade.php"]);
                        \Log::debug("NEW template : ");
                    }else {
                        \DB::table('fel_templates')->whereId($template->id)->update(["blade_resource" => "templates/company/$company->nit/$document_sector/" . $array_custom_docs[$company->nit][0] . ".blade.php"]);
                        \Log::debug("UPDTE template : " . $template->blade_resource);
                    }
                    
                }
            } 
        });
    }
}
