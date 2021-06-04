<?php

namespace EmizorIpx\ClientFel\Providers;

// use Illuminate\Support\ServiceProvider;

use EmizorIpx\ClientFel\Events\Invoice\InvoiceWasEmited;
use EmizorIpx\ClientFel\Events\Invoice\InvoiceWasEmitedUpdate;
use EmizorIpx\ClientFel\Events\Invoice\InvoiceWasReversionRevoked;
use EmizorIpx\ClientFel\Events\Invoice\InvoiceWasRevoked;
use EmizorIpx\ClientFel\Listeners\Invoice\InvoiceEmitedActivity;
use EmizorIpx\ClientFel\Listeners\Invoice\InvoiceEmitedUpdateActivity;
use EmizorIpx\ClientFel\Listeners\Invoice\InvoiceReversionRevokedActivity;
use EmizorIpx\ClientFel\Listeners\Invoice\InvoiceRevokedActivity;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class ClientFelEventServiceProvider extends ServiceProvider
{
    protected $listen = [
        InvoiceWasEmited::class => [
            InvoiceEmitedActivity::class
        ],
        InvoiceWasRevoked::class => [
            InvoiceRevokedActivity::class
        ],
        InvoiceWasEmitedUpdate::class => [
            InvoiceEmitedUpdateActivity::class
        ],
        InvoiceWasReversionRevoked::class => [
            InvoiceReversionRevokedActivity::class
        ]
    ];
    

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }
}
