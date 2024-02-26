<?php

namespace EmizorIpx\ClientFel\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;

class SearchTerrasurRequest extends FormRequest
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
            'numero_contrato' => 'required|integer',
        ];
    
        
    }
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {

            $this->merge([
                'entity' => $this->query('entity')
            ]);
        });
    }


    public function messages()
    {
        return [
            "numero_contrato.required" => "El numero de contrato es un valor requerido",
            "id_servicio.required" => "El id del servicio es un valor requerido",
            "depositar_monto_pago.numeric" => "El monto digitado debe ser un decimal"
        ];
    }
}
