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
    protected $signature = 'emizor:update-tokens {--host=prod : The host environment (prod/dev)}';

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
        $hostEnv = $this->option('host');
        $hostUrl = $hostEnv === 'dev' ? 'https://sinfel.emizor.com' : 'https://fel.emizor.com';

        FelClientToken::where("host", $hostUrl)->cursor()->each(function ($felClienttoken) {
            
            $expiresIn = $felClienttoken->getExpiresIn();
            $updatedAt = $felClienttoken->updated_at;

            if (!empty($expiresIn) && !empty($updatedAt)) {
                $expiresAt = \Carbon\Carbon::parse($updatedAt)->addSeconds((int)$expiresIn);
                
                // Si falta más de 48 horas (2 días) para que expire, saltar
                if (now()->diffInHours($expiresAt, false) > 48) {
                    $this->info("Empresa # {$felClienttoken->account_id} token aún válido. Expira en: {$expiresAt->toDateTimeString()}");
                    return;
                }
            }

            $connection = new Connection($felClienttoken->getHost());

            $clientId = $felClienttoken->getClientId();
            $clientSecret = $felClienttoken->getClientSecret();


            $data = [
                "grant_type" => "client_credentials",
                "client_id" => $clientId,
                "client_secret" => $clientSecret
            ];
            try {
                sleep(1);
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
