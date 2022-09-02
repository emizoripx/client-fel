<?php

namespace EmizorIpx\ClientFel\Traits;

use App\Models\Invoice;

trait NotaServicesTrait {

    public function markDelivered () {

        $this->setStatus( Invoice::STATUS_DELIVERED );

        return $this;
    }

    public function markReceived() {

        $this->setStatus( Invoice::STATUS_RECEIVED );

        return $this;
    }

}