<?php

namespace EmizorIpx\ClientFel\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;

class CobrosQRInvoiceStoreRequest extends FormRequest
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
            ],
            'amount' => "required|numeric|gt:0",
            "ticket" => "required|string",
            "business_name" => "nullable|string",
            "document_number" => "nullable|string",
            "is_nit" => [
                "nullable",
                Rule::in(0,1)
                ]
        ];
    
        
    }
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {

            $ctdi = ($this->has("is_nit") && $this->get('is_nit') == 1) ? 5 : 1;
            $this->merge([
                'montoTotal' => $this->get('amount'),
                'nombreRazonSocial' => $this->get('business_name'),
                'numeroDocumento' => $this->get('document_number'),
                "codigoTipoDocumentIdentidad" => $ctdi,
            ]);
        });
    }


    public function messages()
    {
        return [
            'imei.required' => 'El IMEI es obligatorio.',
            'imei.integer' => 'El IMEI debe ser un número entero.',
            'amount.required' => 'El monto es obligatorio.',
            'amount.numeric' => 'El monto debe ser un valor numérico.',
            'amount.gt' => 'El monto debe ser mayor a 0.',
            'business_name.string' => 'La razón social debe ser una cadena de caracteres.',
            'ticket.required' => 'El ticket es obligatorio.',
            'ticket.string' => 'El ticket debe ser una cadena de caracteres.',
            'document_number.string' => 'El número de documento debe ser una cadena de caracteres.',
            'is_nit.in' => 'El campo is_nit debe ser 0 o 1.',
        ];
    }
}
