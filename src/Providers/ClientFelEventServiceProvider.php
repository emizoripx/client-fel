<?php

namespace EmizorIpx\ClientFel\Providers;

// use Illuminate\Support\ServiceProvider;

use EmizorIpx\ClientFel\Events\Invoice\InvoiceWasEmited;
use EmizorIpx\ClientFel\Events\Invoice\InvoiceWasEmitedUpdate;
use EmizorIpx\ClientFel\Events\Invoice\InvoiceWasReversionRevoked;
use EmizorIpx\ClientFel\Events\Invoice\InvoiceWasRevoked;
use EmizorIpx\ClientFel\Events\Invoice\InvoiceWasWhatsappDelivered;
use EmizorIpx\ClientFel\Events\Invoice\InvoiceWasWhatsappFailed;
use EmizorIpx\ClientFel\Events\Invoice\InvoiceWasWhatsappSent;
use EmizorIpx\ClientFel\Listeners\Invoice\InvoiceEmitedActivity;
use EmizorIpx\ClientFel\Listeners\Invoice\InvoiceEmitedUpdateActivity;
use EmizorIpx\ClientFel\Listeners\Invoice\InvoiceReversionRevokedActivity;
use EmizorIpx\ClientFel\Listeners\Invoice\InvoiceRevokedActivity;
use EmizorIpx\ClientFel\Listeners\Invoice\InvoiceWhatsappDeliveredActivity;
use EmizorIpx\ClientFel\Listeners\Invoice\InvoiceWhatsappFailedActivity;
use EmizorIpx\ClientFel\Listeners\Invoice\InvoiceWhatsappSentActivity;
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
        ],
        InvoiceWasWhatsappSent::class => [
            InvoiceWhatsappSentActivity::class
        ],
        InvoiceWasWhatsappDelivered::class => [
            InvoiceWhatsappDeliveredActivity::class
        ],
        InvoiceWasWhatsappFailed::class => [
            InvoiceWhatsappFailedActivity::class
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
