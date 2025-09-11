<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employment>
 */
class EmploymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_name' => $this->faker->company,
            'employee_id_at_company' => $this->faker->numerify('EMP####'),
            'designation' => $this->faker->jobTitle,
            'location' => $this->faker->city,
            'mode_of_employment' => 'Full-time',
            'start_date' => $this->faker->date(),
            'last_working_date' => $this->faker->optional()->date(),
            'is_current_org' => $this->faker->boolean,
        ];
    }
}
