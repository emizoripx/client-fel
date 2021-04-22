<?php

namespace EmizorIpx\ClientFel\Repository;

use EmizorIpx\ClientFel\Exceptions\ClientFelException;
use EmizorIpx\ClientFel\Models\FelBranch;
use EmizorIpx\ClientFel\Models\FelClientToken;
use EmizorIpx\ClientFel\Models\FelParametric;
use EmizorIpx\ClientFel\Services\Branches\Branches;
use EmizorIpx\ClientFel\Services\Connection\Connection;
use EmizorIpx\ClientFel\Services\Parametrics\Parametric;
use EmizorIpx\ClientFel\Utils\TypeParametrics;

class FelCredentialRepository
{

    private $client_id;

    private $client_secret;
    private $company_id;
    protected $connection;
    private $authenticate_response;
    protected $credential;
    protected $host;

    public function __construct()
    {
        
        $this->client_id = "client_id";
        $this->client_secret = "secret";
        $this->company_id = 0;
        $this->host = 'host';
        $this->authenticate_response = array();
        $this->credential = new FelClientToken();
    }


    public function setCredentials($client_id, $client_secret)
    {
        $this->client_id = $client_id;
        $this->client_secret = $client_secret;

        return $this;
    }
    public function setCompanyId($companyId) {
        $this->company_id = $companyId;

        return $this;
    }

    public function setHost($host) {
        $this->host = $host;

        return $this;
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
            "client_secret"  => $this->client_secret,
            "host"           => $this->host
        ]);

        $this->authenticate();

        $this->updateCredentials();

        return $this;
       
    }

    public function getCredential()
    {
        return $this->credential;
    }

    public function authenticate()
    {
        $this->connection = new Connection($this->credential->getHost());
        $this->authenticate_response = $this->connection->authenticate([
            "grant_type" => "client_credentials",
            "client_id" => $this->client_id,
            "client_secret" => $this->client_secret
        ]);

        return $this;
    }

    public function updateCredentials()
    {

        $this->credential->setTokenType($this->authenticate_response['token_type']);
        $this->credential->setExpiresIn($this->authenticate_response['expires_in']);
        $this->credential->setAccessToken($this->authenticate_response['access_token']);
        $this->credential->save();

        return $this;

    }

    public function syncParametrics()
    {
        $parametricService = new Parametric($this->credential->access_token, $this->credential->host);

        $types = TypeParametrics::getAll();

        foreach ($types as $type) {

            if (FelParametric::existsParametric($type, $this->company_id)) {
                $parametricService->get($type);
                FelParametric::create($type, $parametricService->getResponse(), $this->company_id);
            }
        }
        return $this;

    }

    public function getBranches(){
        $branchService = new Branches($this->credential->access_token, $this->credential->getHost());

        $branches = $branchService->getBranches();

        foreach($branches as $branch){
            if(FelBranch::existsBranch($this->company_id, $branch['codigoSucursal'])){

                FelBranch::create([
                    'codigo' => $branch['codigoSucursal'],
                    'descripcion' => $branch['codigoSucursal'] == 0 ? 'Casa Matriz' : 'Sucursal '.$branch['codigoSucursal'],
                    'company_id' => $this->company_id
                ]);
            }
        }

        return $this;
    }

    
}
