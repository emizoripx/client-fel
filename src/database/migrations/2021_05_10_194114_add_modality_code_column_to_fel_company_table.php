<?php

use EmizorIpx\ClientFel\Models\FelClientToken;
use EmizorIpx\ClientFel\Services\Company\Company;
use EmizorIpx\PrepagoBags\Models\AccountPrepagoBags;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddModalityCodeColumnToFelCompanyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fel_company', function (Blueprint $table) {
            $table->tinyInteger('modality_code')->nullable();

        });

        $felCompany = FelClientToken::all();

        foreach ($felCompany as  $company) {
                try {
                $companyService = new Company($company->access_token, $company->host);
                $response = $companyService->getCompany();

                AccountPrepagoBags::where('company_id', $company->account_id)->update([
                    'modality_code' => $response['modality_code']
                ]);

            } catch (Exception $ex) {
                \Log::debug("Error al obtener datos de Company ". $ex->getMessage());
            }
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fel_company', function (Blueprint $table) {
            $table->dropColumn('modality_code');
        });
    }
}
