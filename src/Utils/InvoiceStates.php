<?php

namespace EmizorIpx\ClientFel\Utils;

class InvoiceStates{
    const INVOICE_STATE_IN_QUEUE = "INVOICE_STATE_IN_QUEUE";
    const INVOICE_STATE_SENT_TO_SIN = "INVOICE_STATE_SENT_TO_SIN";
    const INVOICE_STATE_SENT_TO_SIN_INVALID = "INVOICE_STATE_SENT_TO_SIN_INVALID";
    const INVOICE_STATE_SIN_VALID = "INVOICE_STATE_SIN_VALID";
    const INVOICE_STATE_SIN_INVALID = "INVOICE_STATE_SIN_INVALID";
    const INVOICE_STATE_INTERNAL_ERROR = "INVOICE_STATE_INTERNAL_ERROR";
    const INVOICE_STATE_QUEUE_PENDING = "INVOICE_STATE_QUEUE_PENDING";

    const INVOICE_REVOCATION_STATE_SIN_VALID = "INVOICE_REVOCATION_STATE_SIN_VALID";
    const INVOICE_REVOCATION_STATE_IN_QUEUE = "INVOICE_REVOCATION_STATE_IN_QUEUE";
    const INVOICE_REVOCATION_STATE_SENT_TO_SIN = "INVOICE_REVOCATION_STATE_SENT_TO_SIN";
    const INVOICE_REVOCATION_STATE_SENT_TO_SIN_INVALID = "INVOICE_REVOCATION_STATE_SENT_TO_SIN_INVALID";
    const INVOICE_REVOCATION_STATE_SIN_INVALID = "INVOICE_REVOCATION_STATE_SIN_INVALID";
    const INVOICE_REVOCATION_STATE_INTERNAL_ERROR = "INVOICE_REVOCATION_STATE_INTERNAL_ERROR";

}