<?php
 
use EmizorIpx\ClientFel\Models\FelClient;
use Illuminate\Database\Eloquent\Factories\Factory;

class FelClientFactory extends Factory
{
    
    protected $model = FelClient::class;

    
    public function definition()
    {
        return [
            'type_document_id' => $this->faker->numberBetween(1, 5),
        ];
    }
}
