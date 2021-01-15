<?php 

namespace EmizorIpx\ClientFel;

use EmizorIpx\ClientFel\Http\Middleware\NeedsToken;
use Illuminate\Routing\Router;
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

        $router = $this->app->make(Router::class);

        $router->aliasMiddleware('needs_access_token', NeedsToken::class);
    }

    public function register()
    {

    }
}