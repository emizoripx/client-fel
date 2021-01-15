<?php

namespace EmizorIpx\ClientFel\Models;

use EmizorIpx\ClientFel\Exceptions\ClientFelException;
use Illuminate\Database\Eloquent\Model;

class FelClientToken extends Model
{

    protected $guarded =[];

    public function getAccessToken() 
    {
        return $this->access_token;
    }

    public function getTokenType()
    {
        return $this->token_type;
    }

    public function getClientId()
    {
        return $this->client_id;

    }
    public function getClientSecret()
    {
        return $this->client_secret;
    }

    public function getExpiresIn()
    {
        return $this->expires_in;
    }

    public function setTokenType($value)
    {
        $this->token_type = $value;
    }

    public function setAccessToken($value)
    {
        $this->access_token = $value;
    }

    public function setExpiresIn($value)
    {
        $this->expires_in = $value;
    }

    public static function createOrUpdate($data)
    {
        $credential = self::where('account_id', $data['account_id'])->first();

        if ( empty($credential) ){
            // create
            return self::create($data);
        } else {
            //update
            $credential->update($data);
            return self::find($data['account_id']);
        }
    }

    public static function getTokenByAccount($companyId) 
    {

        if (empty($companyId)) {
            throw new ClientFelException('Id de cuenta inválido');
        }

        $registered_token = self::where('account_id', $companyId)->first();

        if (empty($registered_token) ){
            throw new ClientFelException('No tiene credenciales registradas');
        }
        
        if ( $registered_token->getAccessToken() == null ) {

            throw new ClientFelException('No tiene registrado un access token');
        }

        return $registered_token->getAccessToken();
    }
}
