<?php

use Illuminate\Database\Migrations\Migration;

class ChangeCodigoColumnFelParametrics extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::statement("alter table fel_sector_document_types modify column codigo  varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL");
    
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      
    }
}
