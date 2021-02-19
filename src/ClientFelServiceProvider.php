<?php 

namespace EmizorIpx\ClientFel;

use EmizorIpx\ClientFel\Http\Middleware\NeedsToken;
use EmizorIpx\ClientFel\Observers\FelClientObserver;
use EmizorIpx\ClientFel\Observers\FelInvoiceObserver;
use EmizorIpx\ClientFel\Observers\FelProductObserver;
use EmizorIpx\ClientFel\Repository\ClientFelRepository;
use EmizorIpx\ClientFel\Repository\FelClientRepository;
use EmizorIpx\ClientFel\Repository\FelInvoiceRequestRepository;
use EmizorIpx\ClientFel\Repository\FelProductRepository;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class ClientFelServiceProvider extends ServiceProvider
{

    public function boot()
    {
        # ROUTES
        $this->loadRoutesFrom(__DIR__ . "/routes/Bitacora.php");
        
        # MIGRATIONS
        $this->loadMigrationsFrom(__DIR__ . "/database/migrations");

        # CONFIG FILE
        $this->publishes([
            __DIR__."/config/clientfel.php" => config_path('clientfel.php')
        ]);

        $this->mergeConfigFrom(__DIR__.'/config/clientfel.php', 'clientfel');


        # VIEWS
        $this->loadViewsFrom(__DIR__ . "/Resources/Views", "clientfel");
        
        
        # Middleware
        
        $router = $this->app->make(Router::class);

        $router->aliasMiddleware('needs_access_token', NeedsToken::class);


        # OBSERVERS
        $product = $this->app->make(Config::get('clientfel.entity_table_product'));
        $product::observe(new FelProductObserver(new FelProductRepository));
        
        
        $client = $this->app->make(Config::get('clientfel.entity_table_client'));
        $client::observe(new FelClientObserver(new FelClientRepository));

        $invoice = $this->app->make(Config::get('clientfel.entity_table_invoice'));
        $invoice::observe(new FelInvoiceObserver(new FelInvoiceRequestRepository));

    }
    
    public function register()
    {
        
    }
}