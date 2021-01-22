<?php 

namespace EmizorIpx\ClientFel;

use App\Models\Product;

use EmizorIpx\ClientFel\Http\Middleware\NeedsToken;
use EmizorIpx\ClientFel\Observers\InvoiceFelObserver;
use EmizorIpx\ClientFel\Observers\InvoiceFelService;
use EmizorIpx\ClientFel\Observers\ProductFelObserver;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class ClientFelServiceProvider extends ServiceProvider
{

    public function boot()
    {
        // $this->loadRoutesFrom(__DIR__ . "/routes/api.php");
        $this->loadMigrationsFrom(__DIR__ . "/database/migrations");

        $this->publishes([
            __DIR__."/config/clientfel.php" => config_path('clientfel.php')
        ]);

        $router = $this->app->make(Router::class);

        $router->aliasMiddleware('needs_access_token', NeedsToken::class);

        $product = $this->app->make((Config::get('clientfel.entity_table_product')));
        $product::observe(new ProductFelObserver);
            
        $invoice = $this->app->make(Config::get('clientfel.entity_table_invoice'));
        $invoice::observe(new InvoiceFelObserver);
    }

    public function register()
    {

    }
}