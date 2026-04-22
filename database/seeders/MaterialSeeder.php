<?php

namespace Database\Seeders;

use App\Models\Material;
use Illuminate\Database\Seeder;

class MaterialSeeder extends Seeder
{
    public function run(): void
    {
        $materials = [
            ['grade' => 'S235JR', 'standard' => 'EN 10025-2', 'description' => 'Allgemeiner Baustahl, gut schweißbar', 'density_kg_per_m3' => 7850, 'has_alloy_surcharge' => false, 'is_stainless' => false],
            ['grade' => 'S355J2', 'standard' => 'EN 10025-2', 'description' => 'Hochfester Baustahl, verbesserte Kerbschlagzähigkeit', 'density_kg_per_m3' => 7850, 'has_alloy_surcharge' => false, 'is_stainless' => false],
            ['grade' => 'S355J2+N', 'standard' => 'EN 10025-2', 'description' => 'Hochfester Baustahl, normalgeglüht', 'density_kg_per_m3' => 7850, 'has_alloy_surcharge' => false, 'is_stainless' => false],
            ['grade' => 'DC01', 'standard' => 'EN 10130', 'description' => 'Kaltgewalzter Stahl für Umformzwecke', 'density_kg_per_m3' => 7850, 'has_alloy_surcharge' => false, 'is_stainless' => false],
            ['grade' => '1.4301', 'standard' => 'EN 10088-2', 'description' => 'Austenitischer Edelstahl (V2A), korrosionsbeständig', 'density_kg_per_m3' => 7900, 'has_alloy_surcharge' => true, 'is_stainless' => true],
            ['grade' => '1.4571', 'standard' => 'EN 10088-2', 'description' => 'Austenitischer Edelstahl (V4A), molybdänlegiert, säurebeständig', 'density_kg_per_m3' => 7980, 'has_alloy_surcharge' => true, 'is_stainless' => true],
            ['grade' => '1.4404', 'standard' => 'EN 10088-2', 'description' => 'Edelstahl, kohlenstoffarm, molybdänlegiert', 'density_kg_per_m3' => 7980, 'has_alloy_surcharge' => true, 'is_stainless' => true],
            ['grade' => 'EN AW-6060', 'standard' => 'EN 573-3', 'description' => 'Aluminium-Legierung, gut anodisierbar', 'density_kg_per_m3' => 2700, 'has_alloy_surcharge' => false, 'is_stainless' => false],
            ['grade' => 'EN AW-5754', 'standard' => 'EN 573-3', 'description' => 'Aluminium-Legierung, seewasserbeständig', 'density_kg_per_m3' => 2660, 'has_alloy_surcharge' => false, 'is_stainless' => false],
            ['grade' => 'CW614N', 'standard' => 'EN 12164', 'description' => 'Automatenmessing, gut zerspanbar', 'density_kg_per_m3' => 8470, 'has_alloy_surcharge' => true, 'is_stainless' => false],
            ['grade' => 'Cu-DHP', 'standard' => 'EN 1652', 'description' => 'Phosphor-desoxidiertes Kupfer, hohe Leitfähigkeit', 'density_kg_per_m3' => 8900, 'has_alloy_surcharge' => true, 'is_stainless' => false],
            ['grade' => '51CrV4', 'standard' => 'EN 10089', 'description' => 'Federstahl, vergütet, hohe Elastizität', 'density_kg_per_m3' => 7850, 'has_alloy_surcharge' => true, 'is_stainless' => false],
            ['grade' => 'P265GH', 'standard' => 'EN 10028-2', 'description' => 'Druckbehälterstahl, warmfest', 'density_kg_per_m3' => 7850, 'has_alloy_surcharge' => false, 'is_stainless' => false],
            ['grade' => 'E235+N', 'standard' => 'EN 10305-1', 'description' => 'Präzisionsstahlrohr-Werkstoff, normalgeglüht', 'density_kg_per_m3' => 7850, 'has_alloy_surcharge' => false, 'is_stainless' => false],
            ['grade' => 'S235JRH', 'standard' => 'EN 10210-1', 'description' => 'Baustahl für warmgefertigte Hohlprofile', 'density_kg_per_m3' => 7850, 'has_alloy_surcharge' => false, 'is_stainless' => false],
            ['grade' => 'S355J2H', 'standard' => 'EN 10210-1', 'description' => 'Hochfester Baustahl für Hohlprofile', 'density_kg_per_m3' => 7850, 'has_alloy_surcharge' => false, 'is_stainless' => false],
            ['grade' => 'S235JR+Z275', 'standard' => 'EN 10025-2 / EN 10346', 'description' => 'Feuerverzinkter Baustahl', 'density_kg_per_m3' => 7850, 'has_alloy_surcharge' => false, 'is_stainless' => false],
        ];

        foreach ($materials as $data) {
            Material::create($data);
        }
    }
}
