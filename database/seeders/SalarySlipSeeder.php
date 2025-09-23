<?php

namespace Database\Seeders;

use App\Models\Employment;
use App\Models\SalarySlip;
use Illuminate\Database\Seeder;

class SalarySlipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Employment::all()->each(function ($employment) {
            if ($employment->salarySlips()->count() === 0) {
                SalarySlip::factory()
                    ->count(fake()->numberBetween(1, 3))
                    ->create(['employment_id' => $employment->id]);
            }
        });
    }
}
