<?php

namespace EmizorIpx\ClientFel\Http\ValidationRules\Invoice;

use Illuminate\Validation\Rule;

class ClientRules {

    public static function additionalClientRules(){
        return [
            'name' => 'required|string',
            'id_number' => 'sometimes|string',
            'document_number' => 'sometimes|string',
            'type_document_id' => [
                'required',
                'integer',
                Rule::in([1,2,3,4,5])
            ],
            'complement' => 'nullable|string'
        ];
    }
}