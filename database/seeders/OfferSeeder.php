<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Employee;
use App\Models\Client;
use App\Models\Offer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OfferSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing users, employees, and clients
        $users = User::all();
        $employees = Employee::all();
        $clients = Client::all();

        // Only proceed if we have data to work with
        if ($users->count() > 0 && $employees->count() > 0 && $clients->count() > 0) {
            // Create 10 sample offers with random combinations
            for ($i = 0; $i < 10; $i++) {
                Offer::factory()->create([
                    'user_id' => $users->random()->id,
                    'employee_id' => $employees->random()->id,
                    'department_id' => $clients->random()->id,
                ]);
            }
        }
    }
}