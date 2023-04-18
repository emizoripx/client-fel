<?php

namespace EmizorIpx\ClientFel\Traits;

use EmizorIpx\ClientFel\Services\Invoices\HandleCancellationPending;

trait HandleCancellationPendingTrait {

        public function HandleCancellationPending() {

            $this->invoice = (new HandleCancellationPending($this->invoice))->run();

            return $this;
        }

}
