<?php

namespace Database\Seeders;

use App\Models\Office;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OfficeSeeder extends Seeder
{
    private $offices = [
        [
            'name' => 'BEO Park 1',
            'address_line_1' => "P J Antony Cross Road",
            'address_line_2' => "Palarivattom, Cochin-682 025",
        ],
        [
            'name' => 'BEO Park 2',
            'address_line_1' => "Infopark Expressway",
            'address_line_2' => "Kakkanad, Cochin-682 030",
        ]
    ];
    
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->offices as $office) {
            
            Office::factory()->create($office);
        }
    }
}
