<?php

namespace EmizorIpx\ClientFel\Exceptions;

use Exception;

class WhatsappException extends Exception
{
    public function __construct($msg)
    {
        $finalMessage = 'Errors';

        if ($msg != null) {
            $finalMessage = $msg;
        }

        parent::__construct($finalMessage);
    }
}
