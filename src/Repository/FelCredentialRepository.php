<?php

namespace EmizorIpx\ClientFel\Repository;

use EmizorIpx\ClientFel\Exceptions\ClientFelException;
use EmizorIpx\ClientFel\Models\FelBranch;
use EmizorIpx\ClientFel\Models\FelClientToken;
use EmizorIpx\ClientFel\Models\FelParametric;
use EmizorIpx\ClientFel\Models\FelPOS;
use EmizorIpx\ClientFel\Services\Branches\Branches;
use EmizorIpx\ClientFel\Services\Company\Company;
use EmizorIpx\ClientFel\Services\Connection\Connection;
use EmizorIpx\ClientFel\Services\Parametrics\Parametric;
use EmizorIpx\ClientFel\Services\Pos\Pos;
use EmizorIpx\ClientFel\Utils\TypeParametrics;
use EmizorIpx\PrepagoBags\Models\AccountPrepagoBags;

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
    public function setCredential(FelClientToken $felClientToken)
    {
        $this->credential = $felClientToken;
        return $this;
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

            if (FelParametric::existsParametric($type, $this->credential->account_id)) {
                $parametricService->get($type);
                FelParametric::create($type, $parametricService->getResponse(), $this->credential->account_id);
            }
        }
        return $this;

    }

    public function getBranches(){
        $branchService = new Branches($this->credential->access_token, $this->credential->getHost());

        $branches = $branchService->getBranches();

        foreach($branches as $branch){
            if(FelBranch::existsBranch($this->credential->account_id, $branch['codigoSucursal'])){

                $branch = FelBranch::create([
                    'codigo' => $branch['codigoSucursal'],
                    'descripcion' => $branch['codigoSucursal'] == 0 ? 'Casa Matriz' : 'Sucursal '.$branch['codigoSucursal'],
                    'company_id' => $this->credential->account_id,
                    'zona' => $branch['zona'],
                    'pais' => $branch['pais'],
                    'ciudad' => $branch['ciudad'],
                    'municipio' => $branch['municipio']
                ]);

                $this->getPOS($branch);
            }
        }

        return $this;
    }

    public function getPOS($branch){

        $posService = new Pos($this->credential->access_token, $this->credential->getHost());

        $pos = $posService->setBranch($branch->codigo)->getPOS();

        foreach($pos as $p){
            if(FelPOS::existsPOS($branch->company_id, $branch->codigo, $p['codigo'])){
                FelPOS::create([
                    'codigo' => $p['codigo'],
                    'descripcion' => $p['descripcion'],
                    'branch_id' => $branch->id,
                    'company_id' => $branch->company_id
                ]);
            }
        }

    }

    public function updateFelCompany(){

        $companyService = new Company($this->credential->access_token, $this->credential->host);

        $felCompany = $companyService->getCompany();

        AccountPrepagoBags::where('company_id', $this->credential->account_id)->update([
            'fel_company_id' => $felCompany['id'],
            'modality_code' => $felCompany['modality_code']
        ]);

        return $this;

    }

    
}
