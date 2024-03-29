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
use EmizorIpx\ClientFel\Services\Templates\Templates;
use EmizorIpx\ClientFel\Utils\TypeParametrics;
use EmizorIpx\PrepagoBags\Models\AccountPrepagoBags;
use EmizorIpx\PrepagoBags\Models\PostpagoPlanCompany;
use EmizorIpx\PrepagoBags\Repository\AccountPrepagoBagsRepository;
use Exception;
use Carbon\Carbon;

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

    public function getBranches( $is_postpago = false ){
        $branchService = new Branches($this->credential->access_token, $this->credential->getHost());

        $branches = $branchService->getBranches();

        if($is_postpago){
            $postpago_plan = PostpagoPlanCompany::where('company_id', $this->credential->account_id)->first();
            $counter_branches = AccountPrepagoBagsRepository::getCounterBranches($this->credential->account_id);
        }

        foreach($branches as $branch){
            if(FelBranch::existsBranch($this->credential->account_id, $branch['codigoSucursal'])){

                // TODO: Validate branch counters and enable overflow
                if($is_postpago && $postpago_plan->service()->verifyLimitBranches($counter_branches) && !$postpago_plan->enable_overflow){
                    \Log::debug("LLego al limite de Sucursales");
                    bitacora_info("SyncBranches:CreateBranch", 'Ya llego al limite de suscursales company: ' . $this->credential->account_id);
                    return $this;
                }
                $branch = FelBranch::create([
                    'codigo' => $branch['codigoSucursal'],
                    'descripcion' => $branch['codigoSucursal'] == 0 ? 'Casa Matriz' : 'Sucursal '.$branch['codigoSucursal'],
                    'company_id' => $this->credential->account_id,
                    'zona' => $branch['zona'],
                    'telefono' => $branch['telefono'],
                    'pais' => $branch['pais'],
                    'ciudad' => $branch['ciudad'],
                    'municipio' => $branch['municipio']
                ]);
                \Log::debug("Created Branch ". $branch['codigoSucursal']);

                AccountPrepagoBagsRepository::updateCounterBranches($this->credential->account_id);
                $counter_branches = AccountPrepagoBagsRepository::getCounterBranches($this->credential->account_id);

                $this->getPOS($branch);
            }
        }

        return $this;
    }

    public function getTemplates() {

        try {

            $templateServices = new Templates( $this->credential->access_token, $this->credential->getHost() );
    
            $templates = $templateServices->getTemplates();
    
            $array_input = [];
    
            \Log::debug("Template Response: " . json_encode($templates));
            \Log::debug("Template Response: " . $this->company_id);
    
            foreach ($templates as $template) {
                if( $templateServices->exitsTemplate($this->company_id, $template['document_sector_code'] ,$template['codigoSucursal']  ) ) {
                    array_push($array_input, [
                        "display_name" => $template['display_name'],
                        "document_sector_code" => $template['document_sector_code'],
                        "blade_resource" => $template['blade_resource'],
                        "branch_code" => $template['codigoSucursal'],
                        "created_at" => Carbon::now()->toDateTimeString(),
                        "updated_at" => Carbon::now()->toDateTimeString(),
                        "company_id" => $this->company_id,
                    ]);
                }
            }
    
            \Log::debug("Array Templates " . json_encode($array_input));
            if( count($array_input) > 0 ){
                \DB::table('fel_templates')->insert($array_input);
                \Log::debug("Insert Templates >>>>>>>>>>>>>>>>> ");
            }

            return $this;

        } catch(Exception | ClientFelException $ex){
            \Log::debug("Ocurrio un Error al sincronizar templates: " . $ex->getMessage());
        }


    }

    public function getPOS($branch){
        \Log::debug("GET POS >> ingresando al servicio");
        $posService = new Pos($this->credential->access_token, $this->credential->getHost());
        \Log::debug("GET POS >> servicio consumiendo a " . $this->credential->getHost() );
        $pos = $posService->setBranch($branch->codigo)->getPOS();
        \Log::debug("GET POS >> respuesta de servicio " . json_encode($pos) );
        foreach($pos as $p){
            \Log::debug("GET POS >> POS: " . json_encode($p));
            if(FelPOS::existsPOS($branch->company_id, $branch->codigo, $p['codigo'])){
                FelPOS::create([
                    'codigo' => $p['codigo'],
                    'descripcion' => $p['descripcion'],
                    'branch_id' => $branch->id,
                    'company_id' => $branch->company_id
                ]);
                \Log::debug("GET POS >> POS: created " . json_encode($p));
            }
        }

    }

    public function updateFelCompany(){

        $companyService = new Company($this->credential->access_token, $this->credential->host);

        $felCompany = $companyService->getCompany();

        AccountPrepagoBags::where('company_id', $this->credential->account_id)->update([
            'fel_company_id' => $felCompany['id'],
            'modality_code' => $felCompany['modality_code'],
            'business_name' => $felCompany['business_name'],
            'is_uniper' => $felCompany['is_uniper']
        ]);

        return $this;

    }

    
}
