<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Condition;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'condition_id'=> Condition::factory(),
            'name' => $this->faker->word(),
            'price' => $this->faker->numberBetween(100, 10000),
            'description' => $this->faker->sentence(),
            'brand' => 'ブランド名',
            'item_image' => 'sample.jpg',
            'status' => '0',
        ];
    }
}
