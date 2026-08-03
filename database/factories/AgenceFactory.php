<?php

namespace Database\Factories;

use App\Models\Agence;
use Illuminate\Database\Eloquent\Factories\Factory;

class AgenceFactory extends Factory
{
    protected $model = Agence::class;

    public function definition()
    {
        return [
            'nom_agence' => $this->faker->company,
            'ville' => $this->faker->city,
            'adresse' => $this->faker->address,
            'telephone' => $this->faker->phoneNumber,
        ];
    }
}