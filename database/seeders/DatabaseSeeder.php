<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Document;
use App\Models\Education;
use App\Models\Employee;
use App\Models\Employment;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role' => 'admin'
        ]);

        User::factory(10)->create(['role' => 'employee'])->each(function ($user) {
            Employee::factory()
                ->has(Address::factory()->count(2))
                ->has(Document::factory()->count(3))
                ->has(Education::factory()->count(2), 'educations')
                ->has(Employment::factory()->count(2))
                ->create(['user_id' => $user->id, 'email' => $user->email]);
        });
    }
}