<?php

use App\Models\Account;

class UpdatePlanAccount
{
    /**
     * Run the patch.
     *
     * @return void
     */
    public function run()
    {
        try {
            $accounts = Account::all();

            foreach ($accounts as $account) {
                $account->plan = 'enterprise';
                $account->save();
            }
        } catch (Exception $ex) {
            \Log::debug("Error al Actualizar datos de la Cuenta ". $ex->getMessage());
        }
    }
}
