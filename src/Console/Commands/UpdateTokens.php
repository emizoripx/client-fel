<?php

namespace EmizorIpx\ClientFel\Console\Commands;

use EmizorIpx\ClientFel\Models\FelClientToken;
use EmizorIpx\ClientFel\Services\Connection\Connection;
use Exception;
use Illuminate\Console\Command;

class UpdateTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emizor:update-tokens';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'update tokens';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        FelClientToken::cursor()->each(function ($felClienttoken) {
            
            $connection = new Connection($felClienttoken->getHost());

            $clientId = $felClienttoken->getClientId();
            $clientSecret = $felClienttoken->getClientSecret();


            $data = [
                "grant_type" => "client_credentials",
                "client_id" => $clientId,
                "client_secret" => $clientSecret
            ];
            try {
                sleep(5);
                $response = $connection->authenticate($data);

                $felClienttoken->setTokenType($response['token_type']);
                $felClienttoken->setExpiresIn($response['expires_in']);
                $felClienttoken->setAccessToken($response['access_token']);
                $felClienttoken->save();

                $this->info("Empresa # $felClienttoken->account_id  was updated token con client_id : " . $clientId . " client_secret : " . $clientSecret . " con host  " . $felClienttoken->getHost());
                
            } catch (Exception $ex) {
                $this->warn("NO SE PUEDE AUTENTICAR LA EMPRESA # " . $felClienttoken->account_id . " con client_id : " . $clientId . " client_secret : " . $clientSecret . " con host  " . $felClienttoken->getHost() .' ERROR: '. $ex->getMessage());
            }
        });
    }
}
