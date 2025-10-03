<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employee>
 */
class EmployeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName,
            'middle_name' => $this->faker->optional()->firstName,
            'last_name' => $this->faker->lastName,
            'fathers_name' => $this->faker->name('male'),
            'dob' => $this->faker->date(),
            'gender' => $this->faker->randomElement(['male', 'female', 'other']),
            'marital_status' => $this->faker->randomElement(['single', 'married', 'divorced', 'widowed']),
            'nationality' => 99,
            'place_of_birth' => $this->faker->city,
            'email' => $this->faker->unique()->safeEmail,
            'mobile' => $this->faker->unique()->phoneNumber,
            'photo_path' => null,
            'blood_group' => $this->faker->randomElement(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']),
            'status' => $this->faker->randomElement([0, 1, 2, 3, 4]),
            'offer_letter_status' => 0, //$this->faker->randomElement([0, 1, 2]),
        ];
    }
}
