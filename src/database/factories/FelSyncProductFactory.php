<?php
 
namespace EmizorIpx\ClientFel\Database\factories;

use EmizorIpx\ClientFel\Models\FelSyncProduct;
use Illuminate\Database\Eloquent\Factories\Factory;

class FelSyncProductFactory extends Factory
{
    
    protected $model = FelSyncProduct::class;

    
    public function definition()
    {
        $activitySINProductList = [
            620100 => 83141,
            620901 => 83159
        ];
        $activity = $this->faker->randomElement(array_keys($activitySINProductList));

        $units = [
            4 => 'BOLSA',
            6 => 'CAJA',
            63 => 'ONZA TROY'
        ];

        $unit = $this->faker->randomElement(array_keys($units));

        return [
            'codigo_actividad_economica' => $activity,
            'codigo_producto_sin' => $activitySINProductList[$activity],
            'codigo_unidad' => $unit,
            'nombre_unidad' => $units[$unit],
            'codigo_nandina' => $this->faker->randomElement(array('2616.10.00.00', '2608.00.00.00')),
        ];
    }
}
