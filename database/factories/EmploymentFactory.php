<?php

namespace Database\Factories;

use App\Models\SalarySlip;
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
            'mode_of_employment' => $this->faker->randomElement(['Full-time', 'Part-time', 'Contract', 'Freelance']),
            'start_date' => $this->faker->date(),
            'last_working_date' => $this->faker->optional()->date(),
            'resignation_acceptance_letter_file' => $this->faker->optional()->filePath(),
            'resignation_acceptance_letter_preview_url' => $this->faker->optional()->url(),
            'experience_letter_file' => $this->faker->optional()->filePath(),
            'experience_letter_preview_url' => $this->faker->optional()->url(),
            'is_current_org' => $this->faker->boolean,
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function ($employment) {
            SalarySlip::factory()
                ->count(3)
                ->create(['employment_id' => $employment->id]);
        });
    }
}
