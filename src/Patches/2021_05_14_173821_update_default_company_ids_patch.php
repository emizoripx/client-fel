<?php
use App\Models\Account;

class UpdateDefaultCompanyIdsPatch
{
    /**
     * Run the patch.
     *
     * @return void
     */
    public function run()
    {


        Account::cursor()->each(function ($account) {
            $companies = $account->companies->pluck('id');
            $users = $account->users->pluck('id');

            \Log::debug("ACCOUNT-ID = $account->id  , COMPANIES = " . json_encode($companies) ."  USERS = " .json_encode($users));

            if ( collect($users)->count() == 0 || collect($companies)->count() == 0 ) {
                \Log::debug("borrar porque no tiene usuarios  account # $account->id ");
                $account->delete();
            } else {
                $account->default_company_id = collect($companies)->first();
                \Log::debug("seteando default company  $account->default_company_id");
                $account->save();
            }
        });
        
    }
}
