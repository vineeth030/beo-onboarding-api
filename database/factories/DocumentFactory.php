<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Document>
 */
class DocumentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => $this->faker->randomElement(['pan', 'aadhar', 'passport', 'driving_license', 'voter_id']),
            'number' => $this->faker->unique()->numerify('##########'),
            'name_on_doc' => $this->faker->name,
            'file_path' => '/path/to/document.pdf',
        ];
    }
}
