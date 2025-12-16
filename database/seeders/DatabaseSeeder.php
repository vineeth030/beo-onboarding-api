<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
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

        User::factory(10)->create(['role' => 'candidate'])->each(function ($user) {
            Employee::factory()
                ->has(Address::factory()->state(['type' => 'current']))
                ->has(Address::factory()->state(['type' => 'permanent']))
                ->has(Document::factory()->count(3))
                ->has(Education::factory()->count(2), 'educations')
                ->has(Employment::factory()->count(2))
                ->create(['user_id' => $user->id, 'department_id' => rand(1, 9), 'designation_id' => rand(1, 9), 'office_id' => rand(1,2), 'email' => $user->email, 'password' => 'password']);
        });

        $this->call(OfferSeeder::class);

        DB::table('salary_components')->insert([
            'basic_percentage' => 0.45,
            'da_percentage' => 0.15,
            'hra_percentage' => 0.1375,    
            'travel_allowance_percentage' => 0.03,
            'communication_allowance_threshold' => 40000,
            'communication_allowance_amount' => 2000,
            'research_allowance_threshold' => 50000,
            'research_allowance_amount' => 1000,
            'insurance_internal' => 5985,    
            'insurance_external' => 14400,
            'employer_pf_annual' => 21600
        ]);

        $this->call(ActivitySeeder::class);
    }
}