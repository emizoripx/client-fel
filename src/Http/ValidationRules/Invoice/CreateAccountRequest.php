<?php

namespace EmizorIpx\ClientFel\Http\ValidationRules\Invoice;

use Illuminate\Validation\Rule;

class CreateAccountRequest {

    public static function additionalAccountRules(){
        return [
            'phone_user'  => 'required',
            'nit'  => 'string',
            'razon_social'  => 'string',
            'telefono'  => 'string',
            'company_name'  => 'string',
        ];
    }
}