<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\ClientEmail;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Client::factory()
            ->count(10)
            ->has(ClientEmail::factory()->count(rand(2, 3)))
            ->create();
    }
}
