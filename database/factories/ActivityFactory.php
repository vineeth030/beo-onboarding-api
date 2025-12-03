<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Activity>
 */
class ActivityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'employee_id' => Employee::inRandomOrder()->value('id') ?? 1,
            'performed_by_user_id' => User::inRandomOrder()->value('id') ?? 1,
            'user_type' => $this->faker->randomElement(['candidate', 'hr', 'superadmin']),
            'type' => $this->faker->randomElement(['login', 'logout', 'profile_update', 'document_upload', 'status_change', 'email_sent', 'form_submission']),
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->optional()->sentence(),
            'payload' => $this->faker->optional()->randomElement([
                ['action' => 'created', 'entity' => 'document'],
                ['action' => 'updated', 'entity' => 'profile', 'field' => 'email'],
                ['file_name' => 'document.pdf', 'size' => $this->faker->numberBetween(1000, 5000000)],
            ]),
            'ip_address' => $this->faker->ipv4(),
            'user_agent' => $this->faker->userAgent(),
        ];
    }
}
