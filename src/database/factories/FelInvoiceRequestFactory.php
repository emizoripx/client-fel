<?php
 


use EmizorIpx\ClientFel\Models\FelInvoiceRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

class FelInvoiceRequestFactory extends Factory
{
    
    protected $model = FelInvoiceRequest::class;

    
    public function definition()
    {

        return [
            'codigoMetodoPago' => $this->faker->randomElement(array(1,3)),
            'codigoLeyenda' => 59,
            'codigoSucursal' => 0,
            'codigoPuntoVenta' => 0,
            'codigoMoneda' => 1,
            'tipoCambio' => 1.00,
            'type_invoice_id' => 1,
            'codigoActividad' => $this->faker->randomElement(array(620100, 620901)),
        ];
    }
}
