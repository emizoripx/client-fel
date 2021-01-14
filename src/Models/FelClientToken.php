<?php

namespace EmizorIpx\ClientFel\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FelClientToken extends Model
{
    use HasFactory;

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
}
