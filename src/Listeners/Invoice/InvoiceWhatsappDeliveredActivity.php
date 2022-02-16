<?php

namespace EmizorIpx\ClientFel\Listeners\Invoice;

use App\Libraries\MultiDB;
use App\Models\Activity;
use App\Repositories\ActivityRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use stdClass;

class InvoiceWhatsappDeliveredActivity
{
    protected $activity_repo;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct( ActivityRepository $activity_repo )
    {
        $this->activity_repo = $activity_repo;
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        \Log::debug("Evento Capturado Whatsapp Delivered");
        
        // MultiDB::setDefaultDatabase();

        $fields = new stdClass;

        $user_id = array_key_exists('user_id', $event->event_vars) ? $event->event_vars['user_id'] : $event->invoice->user_id;

        $fields->invoice_id = $event->invoice->id;
        $fields->client_id = $event->invoice->client_id;
        $fields->user_id = $user_id;
        $fields->company_id = $event->invoice->company_id;
        $fields->activity_type_id = Activity::WHATSAPP_DELIVERED_INVOICE ;
        $fields->notes = "Factura entregada satisfactoriamente al teléfono +" . $event->phone_number;

        $this->activity_repo->save($fields, $event->invoice, $event->event_vars);
 
        

    }
}
