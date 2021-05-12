<?php

namespace EmizorIpx\ClientFel\Http\ValidationRules\Invoice;


class StoreInvoiceRules{

    public static function additionalRules(){

        return [
            'line_items.*.product_id' => [
                new CheckProduct()
            ]
        ];

    }

}