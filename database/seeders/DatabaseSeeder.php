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
        $this->call(OfficeSeeder::class);

        User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'superadmin@ob.com',
            'role' => 'superadmin'
        ]);

        $this->call(ClientSeeder::class);

        User::factory(10)->create(['role' => 'candidate'])->each(function ($user) {
            Employee::factory()
                ->has(Address::factory()->state(['type' => 'current']))
                ->has(Address::factory()->state(['type' => 'permanent']))
                ->has(Document::factory()->count(3))
                ->has(Education::factory()->count(2), 'educations')
                ->has(Employment::factory()->count(2))
                ->create(['user_id' => $user->id, 'client_id' => rand(1, 9), 'office_id' => rand(1,2), 'email' => $user->email, 'password' => 'password']);
        });

        $this->call(OfferSeeder::class);
    }
}