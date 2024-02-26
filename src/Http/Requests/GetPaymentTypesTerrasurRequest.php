<?php

namespace EmizorIpx\ClientFel\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class GetPaymentTypesTerrasurRequest extends FormRequest
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

        if ($this->route('payment_type') == 'services') {
            return [
                
            ];
        }else {
            return [
                'numero_contrato' => 'required|min:3',
            ];
        }
        
    }
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {

            $this->merge([
                'paymentType' =>$this->route('payment_type')
            ]);
        });
    } 


    public function messages()
    {
        return [
            "numero_contrato.required" => "El numero de contrato es un valor requerido"
        ];
    }
}
