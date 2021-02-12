<?php 
namespace EmizorIpx\ClientFel\Facades;

use Illuminate\Support\Facades\Facade;

class ClientFel extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'clientfel';
    }
}