<?php

namespace Database\Seeders;

use App\Models\Wholesaler;
use Illuminate\Database\Seeder;

class WholesalerSeeder extends Seeder
{
    public function run(): void
    {
        Wholesaler::create([
            'name' => 'Müller Stahl & Metall GmbH',
            'slug' => 'mueller-stahl-metall',
            'primary_color' => '#1e3a8a',
            'accent_color' => '#f97316',
            'address' => 'Industriestraße 17, 88250 Weingarten',
            'phone' => '+49 751 3606-0',
            'email' => 'info@mueller-stahl.de',
            'founded_year' => 1952,
        ]);
    }
}
