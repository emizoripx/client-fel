<?php

namespace EmizorIpx\ClientFel\Http\ValidationRules\Invoice;

use Illuminate\Validation\Rule;

class ImagesRules {


    public static function validationRules(){
        return [
            'mimes:jpeg,jpg,png,gif',
            'max:10000',
            Rule::dimensions()->maxWidth(350)->maxHeight(350)
        ];
    }
}