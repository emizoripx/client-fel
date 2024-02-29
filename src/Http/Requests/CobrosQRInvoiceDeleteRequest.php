<?php

namespace EmizorIpx\ClientFel\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CobrosQRInvoiceDeleteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        return [
            'imei' => [
                'required',
                'integer',
            ]
        ];
    
        
    }


    public function messages()
    {
        return [
            'imei.required' => 'El IMEI es obligatorio.',
            'imei.integer' => 'El IMEI debe ser un número entero.',
        ];
    }
}
