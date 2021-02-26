<?php


namespace EmizorIpx\ClientFel\Http\ValidationRules\Invoice;

class InvoiceRules {

    public static function aditionalInvoiceRules(){
        return [
            'name' => 'required|string',
            'id_number' => 'required|string',
            'type_document_id' => 'required|integer'
        ];
    }
}