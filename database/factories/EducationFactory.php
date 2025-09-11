<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Education>
 */
class EducationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'board' => $this->faker->word,
            'school' => $this->faker->company . ' School',
            'specialization' => $this->faker->jobTitle,
            'percentage' => $this->faker->randomFloat(2, 50, 100) . '%',
            'from_date' => $this->faker->date(),
            'to_date' => $this->faker->date(),
            'mode_of_education' => 'Full-time',
            'certificate_path' => '/path/to/certificate.pdf',
            'is_highest' => $this->faker->boolean,
        ];
    }
}
