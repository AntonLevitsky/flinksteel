<?php

namespace Database\Seeders;

use App\Models\AnarbeitungOption;
use Illuminate\Database\Seeder;

class AnarbeitungSeeder extends Seeder
{
    public function run(): void
    {
        $options = [
            ['code' => 'saw_cut', 'name_de' => 'Zuschnitt/Sägen', 'price_per_cut_eur' => 3.50, 'price_per_kg_eur' => null],
            ['code' => 'deburr', 'name_de' => 'Entgraten', 'price_per_cut_eur' => 1.20, 'price_per_kg_eur' => null],
            ['code' => 'sandblast', 'name_de' => 'Sandstrahlen', 'price_per_cut_eur' => null, 'price_per_kg_eur' => 0.45],
            ['code' => 'galvanize', 'name_de' => 'Verzinken', 'price_per_cut_eur' => null, 'price_per_kg_eur' => 1.80],
            ['code' => 'prime', 'name_de' => 'Grundieren', 'price_per_cut_eur' => null, 'price_per_kg_eur' => 0.95],
        ];

        foreach ($options as $data) {
            AnarbeitungOption::create($data);
        }
    }
}
