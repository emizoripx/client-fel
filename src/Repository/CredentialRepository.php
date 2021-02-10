<?php

namespace EmizorIpx\ClientFel\Repository;

use EmizorIpx\ClientFel\Exceptions\ClientFelException;
use EmizorIpx\ClientFel\Models\FelClientToken;
use EmizorIpx\ClientFel\Models\FelParametric;
use EmizorIpx\ClientFel\Services\Connection\Connection;
use EmizorIpx\ClientFel\Services\Parametrics\Parametric;
use EmizorIpx\ClientFel\Utils\TypeParametrics;

class CredentialRepository
{

    private $client_id;

    private $client_secret;
    private $company_id;
    protected $connection;
    private $authenticate_response;
    protected $credential;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        $this->client_id = "client_id";
        $this->client_secret = "secret";
        $this->company_id = 0;
        $this->authenticate_response = array();
        $this->credential = new FelClientToken();
    }


    public function setCredentials($client_id, $client_secret)
    {
        $this->client_id = $client_id;
        $this->client_secret = $client_secret;
    }
    public function setCompanyId($companyId) {
        $this->company_id = $companyId;
    }

    public function hasCredentials()
    {
        if (!$this->client_id ) 
            throw new ClientFelException("Es requerido el client_id");

        if (!$this->client_secret) 
            throw new ClientFelException("Es requerido el client_secret");

    }

    public function hasCompanyId()
    {
        if (!$this->company_id) 
            throw new ClientFelException("Es requerido el Id de la companñia");
    }


    public function register()
    {
        $this->hasCredentials();

        $this->hasCompanyId();

        $this->credential = FelClientToken::createOrUpdate([
            "grant_type"     => "client_credentials",
            "account_id"     => $this->company_id,
            "client_id"      => $this->client_id,
            "client_secret"  => $this->client_secret
        ]);

        $this->authenticate();

        $this->updateCredentials();
       
    }

    public function getCredential()
    {
        return $this->credential;
    }

    public function authenticate()
    {
        $this->authenticate_response = $this->connection->authenticate([
            "grant_type" => "client_credentials",
            "client_id" => $this->client_id,
            "client_secret" => $this->client_secret
        ]);
    }

    public function updateCredentials()
    {

        $this->credential->setTokenType($this->authenticate_response['token_type']);
        $this->credential->setExpiresIn($this->authenticate_response['expires_in']);
        $this->credential->setAccessToken($this->authenticate_response['access_token']);
        $this->credential->save();

    }

    public function syncParametrics()
    {
        $parametricService = new Parametric($this->credential->access_token);

        $types = TypeParametrics::getAll();

        foreach ($types as $type) {

            if (FelParametric::existsParametric($type, $this->company_id)) {
                $parametricService->get($type);
                FelParametric::create($type, $parametricService->getResponse(), $this->company_id);
            }
        }

    }


    
}
