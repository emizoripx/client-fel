<?php

namespace EmizorIpx\ClientFel\Events\Invoice;

use App\Models\Company;
use App\Models\Invoice;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InvoiceWasEmited
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $invoice;
    public $company;
    public $event_vars;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct( Invoice $invoice, Company $company, array $event_vars )
    {
        $this->invoice = $invoice;
        $this->company = $company;
        $this->event_vars = $event_vars;
    }
}
