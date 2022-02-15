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

class InvoiceWasWhatsappSent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $invoice;
    public $event_vars;
    public $phone_number;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct( Invoice $invoice, array $event_vars, string $phone_number )
    {
        $this->invoice = $invoice;
        $this->event_vars = $event_vars;
        $this->phone_number = $phone_number;
    }
}
