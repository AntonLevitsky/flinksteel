<?php

namespace Database\Seeders;

use App\Models\Form;
use Illuminate\Database\Seeder;

class FormSeeder extends Seeder
{
    public function run(): void
    {
        $forms = [
            ['name' => 'Rundstahl', 'slug' => 'rundstahl', 'dimension_fields' => ['diameter_mm', 'length_mm']],
            ['name' => 'Flachstahl', 'slug' => 'flachstahl', 'dimension_fields' => ['width_mm', 'thickness_mm', 'length_mm']],
            ['name' => 'Winkelstahl', 'slug' => 'winkelstahl', 'dimension_fields' => ['width_mm', 'height_mm', 'thickness_mm', 'length_mm']],
            ['name' => 'IPE-Träger', 'slug' => 'ipe-traeger', 'dimension_fields' => ['height_mm', 'length_mm']],
            ['name' => 'HEA-Träger', 'slug' => 'hea-traeger', 'dimension_fields' => ['height_mm', 'length_mm']],
            ['name' => 'HEB-Träger', 'slug' => 'heb-traeger', 'dimension_fields' => ['height_mm', 'length_mm']],
            ['name' => 'U-Stahl', 'slug' => 'u-stahl', 'dimension_fields' => ['height_mm', 'length_mm']],
            ['name' => 'T-Stahl', 'slug' => 't-stahl', 'dimension_fields' => ['width_mm', 'height_mm', 'thickness_mm', 'length_mm']],
            ['name' => 'Rechteckrohr', 'slug' => 'rechteckrohr', 'dimension_fields' => ['width_mm', 'height_mm', 'wall_thickness_mm', 'length_mm']],
            ['name' => 'Quadratrohr', 'slug' => 'quadratrohr', 'dimension_fields' => ['width_mm', 'wall_thickness_mm', 'length_mm']],
            ['name' => 'Rundrohr', 'slug' => 'rundrohr', 'dimension_fields' => ['outer_diameter_mm', 'wall_thickness_mm', 'length_mm']],
            ['name' => 'Präzisionsstahlrohr', 'slug' => 'praezisionsrohr', 'dimension_fields' => ['outer_diameter_mm', 'wall_thickness_mm', 'length_mm']],
            ['name' => 'Blech', 'slug' => 'blech', 'dimension_fields' => ['width_mm', 'length_mm', 'thickness_mm']],
            ['name' => 'Tränenblech', 'slug' => 'traenenblech', 'dimension_fields' => ['width_mm', 'length_mm', 'thickness_mm']],
        ];

        foreach ($forms as $data) {
            Form::create($data);
        }
    }
}
