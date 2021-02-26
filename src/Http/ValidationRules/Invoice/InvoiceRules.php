<?php


namespace EmizorIpx\ClientFel\Http\ValidationRules\Invoice;

use Illuminate\Validation\Rule;
class InvoiceRules {

    public static function additionalInvoiceRules(){

        // $validator = Validator::make();
        $rule1 = [];

        if(request()->has('name')){
            $rule1 = [
                'name' => 'required|string',
                'id_number' => 'required|string',
                'type_document_id' => [
                    'required',
                    'integer',
                    Rule::in([1,2,3,4,5])
                ]
            ];
        }

        return array_merge($rule1, [
            'line_items.*.product_id' => [
                'required',
                'string',
                new CheckProduct()
            ]
        ]);
    }
}