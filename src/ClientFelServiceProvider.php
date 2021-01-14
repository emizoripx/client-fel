<?php 

namespace EmizorIpx\ClientFel;

use Illuminate\Support\ServiceProvider;

class ClientFelServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . "/routes/api.php");
        $this->loadMigrationsFrom(__DIR__ . "/database/migrations");

        $this->publishes([
            __DIR__."/config/clientfel.php" => config_path('clientfel.php')
        ]);
    }

    public function register()
    {

    }
}