<?php

use App\Models\CompanyUser;

class RemoveSettingToCompanyUsers
{
    /**
     * Run the patch.
     *
     * @return void
     */
    public function run()
    {
        CompanyUser::withTrashed()->cursor()->each( function( $company_user ){
            $company_user->update([
                'settings' => null
            ]);
        });
    }
}
