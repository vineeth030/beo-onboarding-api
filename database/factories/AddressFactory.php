<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Address>
 */
class AddressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'line1' => $this->faker->streetAddress,
            'line2' => $this->faker->optional()->secondaryAddress,
            'landmark' => $this->faker->optional()->word,
            'country' => 100,
            'state' => $this->faker->state,
            'city' => $this->faker->city,
            'pin' => $this->faker->postcode,
            'duration_of_stay' => $this->faker->randomDigit . ' years',
        ];
    }
}
