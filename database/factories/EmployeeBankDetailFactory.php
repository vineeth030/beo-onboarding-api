<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EmployeeBankDetail>
 */
class EmployeeBankDetailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'bank_name' => $this->faker->company.' Bank',
            'account_holder_name' => $this->faker->name,
            'account_number' => $this->faker->numerify('##############'),
            'branch_name' => $this->faker->city,
            'ifsc_code' => $this->faker->regexify('[A-Z]{4}0[A-Z0-9]{6}'),
            'proof_document_path' => '/storage/bank-details/proof.pdf',
        ];
    }
}
