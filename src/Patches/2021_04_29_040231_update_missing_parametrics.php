<?php

use EmizorIpx\ClientFel\Models\FelClientToken;
use EmizorIpx\ClientFel\Exceptions\ClientFelException;
use EmizorIpx\ClientFel\Repository\FelCredentialRepository;

class UpdateMissingParametrics
{
    /**
     * Run the patch.
     *
     * @return void
     */
    public function run()
    {
        $felClienttokens = FelClientToken::get();

        $credentialrepo =  new FelCredentialRepository;

        foreach ($felClienttokens as $felClienttoken) {

            try {

                $credentialrepo
                    ->setCredential($felClienttoken)
                    ->syncParametrics()
                    ->getBranches();

            } catch (ClientFelException $ex) {

                echo "Problemas en la comunicación COMPANY_ID: $felClienttoken->account_id : " . $ex->getMessage() . " \n";
            }
        }
    }
}
