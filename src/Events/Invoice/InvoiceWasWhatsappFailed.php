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

class InvoiceWasWhatsappFailed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $invoice_id;
    public $event_vars;
    public $phone_number;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct( $invoice_id, array $event_vars, string $phone_number )
    {
        $this->invoice = Invoice::find($invoice_id);
        $this->event_vars = $event_vars;
        $this->phone_number = $phone_number;
    }
}
