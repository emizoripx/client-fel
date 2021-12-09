<?php

namespace EmizorIpx\ClientFel\Traits;

trait FelRestoreTrait {

    public function fel_restore()
    {
        if(empty($this->fel_invoice)) {
            return $this;
        }

        if (!is_null($this->fel_invoice->deleted_at)) {
            $this->fel_invoice->deleted_at = null;
            $this->fel_invoice->save();
        }

        return $this;
    }
}