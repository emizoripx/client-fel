<?php

use EmizorIpx\ClientFel\Models\FelClientToken;
use EmizorIpx\PrepagoBags\Models\AccountPrepagoBags;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSettingColumnToFelCompanyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fel_company', function (Blueprint $table) {
            $table->json('settings')->nullable();
        });

        $felTokens = FelClientToken::select(['account_id', 'settings'])->get();

        foreach ($felTokens as $felToken) {
            try {
                AccountPrepagoBags::where('company_id', $felToken->account_id)
                    ->update([
                        'settings' => $felToken->settings
                    ]);
            } catch (Exception $ex) {
                \Log::debug("Error al actualizar settings de company: ". $felToken->account_id . "Message: ".$ex->getMessage());
            }
        }

        Schema::table('fel_company_tokens', function (Blueprint $table) {
            $table->dropColumn('settings');
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fel_company', function (Blueprint $table) {
            //
        });
    }
}
