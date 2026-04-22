<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $topLevel = [
            ['name' => 'Stabstahl', 'slug' => 'stabstahl', 'sort_order' => 1, 'icon' => 'bars', 'description' => 'Rundstahl, Flachstahl, Vierkantstahl und weitere Stabstahlprodukte'],
            ['name' => 'Profilstahl', 'slug' => 'profilstahl', 'sort_order' => 2, 'icon' => 'profile', 'description' => 'IPE, HEA, HEB, UPN und weitere Profilstahlträger'],
            ['name' => 'Bleche', 'slug' => 'bleche', 'sort_order' => 3, 'icon' => 'sheet', 'description' => 'Feinbleche, Grobbleche, Tränenbleche und Sonderbleche'],
            ['name' => 'Rohre', 'slug' => 'rohre', 'sort_order' => 4, 'icon' => 'tube', 'description' => 'Rechteckrohre, Quadratrohre, Rundrohre und Präzisionsstahlrohre'],
            ['name' => 'Edelstahl', 'slug' => 'edelstahl', 'sort_order' => 5, 'icon' => 'stainless', 'description' => 'Edelstahl-Rundstahl, -Flachstahl, -Bleche und -Profile'],
            ['name' => 'NE-Metalle', 'slug' => 'ne-metalle', 'sort_order' => 6, 'icon' => 'nonferrous', 'description' => 'Aluminium, Messing, Kupfer und weitere Nichteisenmetalle'],
        ];

        foreach ($topLevel as $data) {
            Category::create($data);
        }

        $children = [
            'stabstahl' => [
                ['name' => 'Rundstahl', 'slug' => 'rundstahl', 'sort_order' => 1],
                ['name' => 'Flachstahl', 'slug' => 'flachstahl', 'sort_order' => 2],
                ['name' => 'Vierkantstahl', 'slug' => 'vierkantstahl', 'sort_order' => 3],
                ['name' => 'Sechskantstahl', 'slug' => 'sechskantstahl', 'sort_order' => 4],
            ],
            'profilstahl' => [
                ['name' => 'IPE-Träger', 'slug' => 'ipe-traeger', 'sort_order' => 1],
                ['name' => 'HEA-Träger', 'slug' => 'hea-traeger', 'sort_order' => 2],
                ['name' => 'HEB-Träger', 'slug' => 'heb-traeger', 'sort_order' => 3],
                ['name' => 'U-Stahl (UPN)', 'slug' => 'upn', 'sort_order' => 4],
                ['name' => 'T-Stahl', 'slug' => 't-stahl', 'sort_order' => 5],
                ['name' => 'Winkelstahl', 'slug' => 'winkelstahl', 'sort_order' => 6],
            ],
            'bleche' => [
                ['name' => 'Feinbleche', 'slug' => 'feinbleche', 'sort_order' => 1],
                ['name' => 'Grobbleche', 'slug' => 'grobbleche', 'sort_order' => 2],
                ['name' => 'Tränenbleche', 'slug' => 'traenenbleche', 'sort_order' => 3],
                ['name' => 'Sonderbleche', 'slug' => 'sonderbleche', 'sort_order' => 4],
            ],
            'rohre' => [
                ['name' => 'Rechteckrohre', 'slug' => 'rechteckrohre', 'sort_order' => 1],
                ['name' => 'Quadratrohre', 'slug' => 'quadratrohre', 'sort_order' => 2],
                ['name' => 'Rundrohre', 'slug' => 'rundrohre', 'sort_order' => 3],
                ['name' => 'Präzisionsstahlrohre', 'slug' => 'praezisionsstahlrohre', 'sort_order' => 4],
            ],
            'edelstahl' => [
                ['name' => 'Edelstahl Rundstahl', 'slug' => 'edelstahl-rundstahl', 'sort_order' => 1],
                ['name' => 'Edelstahl Flachstahl', 'slug' => 'edelstahl-flachstahl', 'sort_order' => 2],
                ['name' => 'Edelstahlbleche', 'slug' => 'edelstahlbleche', 'sort_order' => 3],
                ['name' => 'Edelstahl Profile', 'slug' => 'edelstahl-profile', 'sort_order' => 4],
            ],
            'ne-metalle' => [
                ['name' => 'Aluminium', 'slug' => 'aluminium', 'sort_order' => 1],
                ['name' => 'Messing', 'slug' => 'messing', 'sort_order' => 2],
                ['name' => 'Kupfer', 'slug' => 'kupfer', 'sort_order' => 3],
                ['name' => 'Sonstiges', 'slug' => 'ne-sonstiges', 'sort_order' => 4],
            ],
        ];

        foreach ($children as $parentSlug => $kids) {
            $parent = Category::where('slug', $parentSlug)->first();
            foreach ($kids as $child) {
                Category::create(array_merge($child, ['parent_id' => $parent->id]));
            }
        }
    }
}
