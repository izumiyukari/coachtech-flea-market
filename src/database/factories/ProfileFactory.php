<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'postal_code'   => $this->faker->regexify('[0-9]{3}-[0-9]{4}'),
            'address'       => $this->faker->address(),
            'building'      => $this->faker->secondaryAddress(),
            'profile_image' => 'sample.jpg',
        ];
    }
}
