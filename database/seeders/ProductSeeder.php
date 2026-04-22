<?php

namespace Database\Seeders;

use App\Models\AnarbeitungOption;
use App\Models\Category;
use App\Models\Form;
use App\Models\Material;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Helper lookups
        $cat = fn(string $slug) => Category::where('slug', $slug)->first()->id;
        $mat = fn(string $grade) => Material::where('grade', $grade)->first()->id;
        $form = fn(string $slug) => Form::where('slug', $slug)->first()->id;
        $aOpt = fn(string $code) => AnarbeitungOption::where('code', $code)->first()->id;

        $products = [
            // 1. Rundstahl blank S235JR Ø20mm 6m
            [
                'category_id' => $cat('rundstahl'),
                'material_id' => $mat('S235JR'),
                'form_id' => $form('rundstahl'),
                'sku' => 'RS-S235-D20',
                'name' => 'Rundstahl blank S235JR, Ø 20 mm',
                'short_description' => 'Blankstahl rund, gezogen, Toleranz h11',
                'long_description' => 'Rundstahl blank aus S235JR nach EN 10277/EN 10278. Oberfläche gezogen (blankgezogen), Toleranz h11. Standardlänge 6.000 mm, Zuschnitt möglich. Geeignet für allgemeinen Maschinenbau, Bolzen, Achsen und Konstruktionsteile.',
                'dimensions' => ['diameter_mm' => 20, 'length_mm' => 6000],
                'weight_per_meter_kg' => 2.466, // π/4 * 0.020² * 7850
                'is_cut_to_length' => true,
                'standard_length_mm' => 6000,
                'price_per_kg_eur' => 1.25,
                'stock_quantity_kg' => 4500,
                'is_featured' => true,
                'certifications_available' => ['2.2', '3.1'],
                'anarbeitung' => ['saw_cut', 'deburr', 'sandblast', 'galvanize'],
            ],
            // 2. Rundstahl blank S355J2 Ø30mm 6m
            [
                'category_id' => $cat('rundstahl'),
                'material_id' => $mat('S355J2'),
                'form_id' => $form('rundstahl'),
                'sku' => 'RS-S355-D30',
                'name' => 'Rundstahl blank S355J2, Ø 30 mm',
                'short_description' => 'Blankstahl rund, hochfest, Toleranz h11',
                'long_description' => 'Rundstahl blank aus S355J2 nach EN 10277. Hochfester Baustahl mit verbesserter Kerbschlagzähigkeit bei -20°C. Standardlänge 6.000 mm.',
                'dimensions' => ['diameter_mm' => 30, 'length_mm' => 6000],
                'weight_per_meter_kg' => 5.549, // π/4 * 0.030² * 7850
                'is_cut_to_length' => true,
                'standard_length_mm' => 6000,
                'price_per_kg_eur' => 1.35,
                'stock_quantity_kg' => 3200,
                'certifications_available' => ['2.2', '3.1'],
                'anarbeitung' => ['saw_cut', 'deburr', 'sandblast'],
            ],
            // 3. Rundstahl blank S355J2 Ø50mm 6m (Restlängen)
            [
                'category_id' => $cat('rundstahl'),
                'material_id' => $mat('S355J2'),
                'form_id' => $form('rundstahl'),
                'sku' => 'RS-S355-D50',
                'name' => 'Rundstahl blank S355J2, Ø 50 mm',
                'short_description' => 'Blankstahl rund, hochfest, Toleranz h11',
                'long_description' => 'Rundstahl blank aus S355J2 nach EN 10277. Standardlänge 6.000 mm. Restlängen aus laufender Produktion verfügbar.',
                'dimensions' => ['diameter_mm' => 50, 'length_mm' => 6000],
                'weight_per_meter_kg' => 15.413, // π/4 * 0.050² * 7850
                'is_cut_to_length' => true,
                'standard_length_mm' => 6000,
                'price_per_kg_eur' => 1.30,
                'stock_quantity_kg' => 0,
                'has_restlaengen' => false,
                'certifications_available' => ['2.2', '3.1', '3.2'],
                'anarbeitung' => ['saw_cut', 'deburr', 'sandblast', 'galvanize'],
            ],
            // 4. Flachstahl S235JR 40x10mm 6m
            [
                'category_id' => $cat('flachstahl'),
                'material_id' => $mat('S235JR'),
                'form_id' => $form('flachstahl'),
                'sku' => 'FS-S235-40x10',
                'name' => 'Flachstahl S235JR, 40 × 10 mm',
                'short_description' => 'Warmgewalzter Flachstahl, Oberfläche schwarz',
                'long_description' => 'Flachstahl warmgewalzt aus S235JR nach EN 10058. Standardlänge 6.000 mm. Vielseitig einsetzbar für Konstruktionen, Geländer, Zäune und allgemeinen Stahlbau.',
                'dimensions' => ['width_mm' => 40, 'thickness_mm' => 10, 'length_mm' => 6000],
                'weight_per_meter_kg' => 3.140, // 0.040 * 0.010 * 7850
                'is_cut_to_length' => true,
                'standard_length_mm' => 6000,
                'price_per_kg_eur' => 1.20,
                'stock_quantity_kg' => 5200,
                'is_featured' => true,
                'certifications_available' => ['2.2', '3.1'],
                'anarbeitung' => ['saw_cut', 'deburr', 'sandblast', 'galvanize', 'prime'],
            ],
            // 5. Flachstahl S235JR 60x8mm 6m
            [
                'category_id' => $cat('flachstahl'),
                'material_id' => $mat('S235JR'),
                'form_id' => $form('flachstahl'),
                'sku' => 'FS-S235-60x8',
                'name' => 'Flachstahl S235JR, 60 × 8 mm',
                'short_description' => 'Warmgewalzter Flachstahl, Oberfläche schwarz',
                'long_description' => 'Flachstahl warmgewalzt aus S235JR nach EN 10058. Standardlänge 6.000 mm.',
                'dimensions' => ['width_mm' => 60, 'thickness_mm' => 8, 'length_mm' => 6000],
                'weight_per_meter_kg' => 3.768, // 0.060 * 0.008 * 7850
                'is_cut_to_length' => true,
                'standard_length_mm' => 6000,
                'price_per_kg_eur' => 1.20,
                'stock_quantity_kg' => 3800,
                'certifications_available' => ['2.2', '3.1'],
                'anarbeitung' => ['saw_cut', 'deburr', 'sandblast', 'galvanize', 'prime'],
            ],
            // 6. Winkelstahl 50x50x5mm S235JR 6m (fixed length)
            [
                'category_id' => $cat('winkelstahl'),
                'material_id' => $mat('S235JR'),
                'form_id' => $form('winkelstahl'),
                'sku' => 'WS-S235-50x50x5',
                'name' => 'Winkelstahl gleichschenklig S235JR, 50 × 50 × 5 mm, 6 m',
                'short_description' => 'Warmgewalzter Winkelstahl, gleichschenklig',
                'long_description' => 'Winkelstahl gleichschenklig warmgewalzt aus S235JR nach EN 10056. Standardlänge 6.000 mm, Festlänge.',
                'dimensions' => ['width_mm' => 50, 'height_mm' => 50, 'thickness_mm' => 5, 'length_mm' => 6000],
                'weight_per_piece_kg' => 22.38, // 3.73 kg/m * 6
                'weight_per_meter_kg' => 3.730,
                'is_cut_to_length' => false,
                'price_per_kg_eur' => 1.22,
                'stock_quantity_kg' => 2600,
                'certifications_available' => ['2.2', '3.1'],
                'anarbeitung' => ['saw_cut', 'deburr', 'galvanize'],
            ],
            // 7. Winkelstahl 80x80x8mm S235JR 6m (fixed length)
            [
                'category_id' => $cat('winkelstahl'),
                'material_id' => $mat('S235JR'),
                'form_id' => $form('winkelstahl'),
                'sku' => 'WS-S235-80x80x8',
                'name' => 'Winkelstahl gleichschenklig S235JR, 80 × 80 × 8 mm, 6 m',
                'short_description' => 'Warmgewalzter Winkelstahl, gleichschenklig',
                'long_description' => 'Winkelstahl gleichschenklig warmgewalzt aus S235JR nach EN 10056. Standardlänge 6.000 mm, Festlänge.',
                'dimensions' => ['width_mm' => 80, 'height_mm' => 80, 'thickness_mm' => 8, 'length_mm' => 6000],
                'weight_per_piece_kg' => 57.36, // 9.56 kg/m * 6
                'weight_per_meter_kg' => 9.560,
                'is_cut_to_length' => false,
                'price_per_kg_eur' => 1.22,
                'stock_quantity_kg' => 1800,
                'certifications_available' => ['2.2', '3.1'],
                'anarbeitung' => ['saw_cut', 'deburr', 'galvanize', 'prime'],
            ],
            // 8. IPE 200 S235JR 12m (fixed length, Restlängen)
            [
                'category_id' => $cat('ipe-traeger'),
                'material_id' => $mat('S235JR'),
                'form_id' => $form('ipe-traeger'),
                'sku' => 'IPE-200-S235',
                'name' => 'IPE 200 S235JR, 12 m',
                'short_description' => 'IPE-Träger, europäischer I-Profilstahl',
                'long_description' => 'IPE 200 aus S235JR nach EN 10025-2 / EN 10034. Höhe 200 mm, Flanschbreite 100 mm, Stegdicke 5,6 mm. Standardlänge 12.000 mm. Restlängen verfügbar.',
                'dimensions' => ['height_mm' => 200, 'length_mm' => 12000],
                'weight_per_piece_kg' => 268.80, // 22.40 kg/m * 12
                'weight_per_meter_kg' => 22.400,
                'is_cut_to_length' => false,
                'price_per_kg_eur' => 1.18,
                'stock_quantity_kg' => 8500,
                'has_restlaengen' => true,
                'is_featured' => true,
                'certifications_available' => ['2.2', '3.1', '3.2'],
                'anarbeitung' => ['saw_cut', 'sandblast', 'galvanize', 'prime'],
            ],
            // 9. HEA 160 S235JR 12m (fixed length)
            [
                'category_id' => $cat('hea-traeger'),
                'material_id' => $mat('S235JR'),
                'form_id' => $form('hea-traeger'),
                'sku' => 'HEA-160-S235',
                'name' => 'HEA 160 S235JR, 12 m',
                'short_description' => 'HEA-Träger, breiter Flansch',
                'long_description' => 'HEA 160 (HEAA) aus S235JR nach EN 10025-2 / EN 10034. Höhe 152 mm, Flanschbreite 160 mm. Standardlänge 12.000 mm.',
                'dimensions' => ['height_mm' => 160, 'length_mm' => 12000],
                'weight_per_piece_kg' => 365.28, // 30.44 kg/m * 12
                'weight_per_meter_kg' => 30.440,
                'is_cut_to_length' => false,
                'price_per_kg_eur' => 1.20,
                'stock_quantity_kg' => 6200,
                'certifications_available' => ['2.2', '3.1'],
                'anarbeitung' => ['saw_cut', 'sandblast', 'galvanize', 'prime'],
            ],
            // 10. HEB 200 S355J2 12m (fixed length)
            [
                'category_id' => $cat('heb-traeger'),
                'material_id' => $mat('S355J2'),
                'form_id' => $form('heb-traeger'),
                'sku' => 'HEB-200-S355',
                'name' => 'HEB 200 S355J2, 12 m',
                'short_description' => 'HEB-Träger, hochfest, breiter Flansch',
                'long_description' => 'HEB 200 aus S355J2 nach EN 10025-2 / EN 10034. Höhe 200 mm, Flanschbreite 200 mm, Stegdicke 9 mm. Standardlänge 12.000 mm.',
                'dimensions' => ['height_mm' => 200, 'length_mm' => 12000],
                'weight_per_piece_kg' => 735.60, // 61.30 kg/m * 12
                'weight_per_meter_kg' => 61.300,
                'is_cut_to_length' => false,
                'price_per_kg_eur' => 1.35,
                'stock_quantity_kg' => 0,
                'certifications_available' => ['2.2', '3.1', '3.2'],
                'anarbeitung' => ['saw_cut', 'sandblast', 'prime'],
            ],
            // 11. UPN 100 S235JR 6m (fixed length)
            [
                'category_id' => $cat('upn'),
                'material_id' => $mat('S235JR'),
                'form_id' => $form('u-stahl'),
                'sku' => 'UPN-100-S235',
                'name' => 'U-Stahl UPN 100 S235JR, 6 m',
                'short_description' => 'U-Profil warmgewalzt',
                'long_description' => 'U-Stahl UPN 100 aus S235JR nach EN 10025-2 / EN 10279. Höhe 100 mm. Standardlänge 6.000 mm.',
                'dimensions' => ['height_mm' => 100, 'length_mm' => 6000],
                'weight_per_piece_kg' => 64.20, // 10.70 kg/m * 6
                'weight_per_meter_kg' => 10.700,
                'is_cut_to_length' => false,
                'price_per_kg_eur' => 1.25,
                'stock_quantity_kg' => 3400,
                'certifications_available' => ['2.2', '3.1'],
                'anarbeitung' => ['saw_cut', 'sandblast', 'galvanize'],
            ],
            // 12. T-Stahl S235JR 50x50x6mm 6m (fixed length)
            [
                'category_id' => $cat('t-stahl'),
                'material_id' => $mat('S235JR'),
                'form_id' => $form('t-stahl'),
                'sku' => 'TS-S235-50x50x6',
                'name' => 'T-Stahl S235JR, 50 × 50 × 6 mm, 6 m',
                'short_description' => 'T-Profil warmgewalzt',
                'long_description' => 'T-Stahl aus S235JR nach EN 10055. Breite 50 mm, Höhe 50 mm, Dicke 6 mm. Standardlänge 6.000 mm.',
                'dimensions' => ['width_mm' => 50, 'height_mm' => 50, 'thickness_mm' => 6, 'length_mm' => 6000],
                'weight_per_piece_kg' => 27.48, // 4.58 kg/m * 6
                'weight_per_meter_kg' => 4.580,
                'is_cut_to_length' => false,
                'price_per_kg_eur' => 1.28,
                'stock_quantity_kg' => 1200,
                'certifications_available' => ['2.2', '3.1'],
                'anarbeitung' => ['saw_cut', 'deburr', 'galvanize'],
            ],
            // 13. Rechteckrohr S235JRH 80x40x4mm 6m (cut-to-length)
            [
                'category_id' => $cat('rechteckrohre'),
                'material_id' => $mat('S235JRH'),
                'form_id' => $form('rechteckrohr'),
                'sku' => 'RR-S235-80x40x4',
                'name' => 'Rechteckrohr warmgefertigt S235JRH, 80 × 40 × 4 mm',
                'short_description' => 'Warmgefertigtes Hohlprofil, rechteckig',
                'long_description' => 'Rechteckrohr warmgefertigt aus S235JRH nach EN 10210. 80 × 40 mm, Wanddicke 4 mm. Standardlänge 6.000 mm.',
                'dimensions' => ['width_mm' => 80, 'height_mm' => 40, 'wall_thickness_mm' => 4, 'length_mm' => 6000],
                'weight_per_meter_kg' => 5.450,
                'is_cut_to_length' => true,
                'standard_length_mm' => 6000,
                'price_per_kg_eur' => 1.30,
                'stock_quantity_kg' => 3600,
                'certifications_available' => ['2.2', '3.1'],
                'anarbeitung' => ['saw_cut', 'deburr', 'sandblast', 'galvanize'],
            ],
            // 14. Quadratrohr S235JRH 50x50x3mm 6m (cut-to-length)
            [
                'category_id' => $cat('quadratrohre'),
                'material_id' => $mat('S235JRH'),
                'form_id' => $form('quadratrohr'),
                'sku' => 'QR-S235-50x50x3',
                'name' => 'Quadratrohr warmgefertigt S235JRH, 50 × 50 × 3 mm',
                'short_description' => 'Warmgefertigtes Hohlprofil, quadratisch',
                'long_description' => 'Quadratrohr warmgefertigt aus S235JRH nach EN 10210. 50 × 50 mm, Wanddicke 3 mm. Standardlänge 6.000 mm.',
                'dimensions' => ['width_mm' => 50, 'wall_thickness_mm' => 3, 'length_mm' => 6000],
                'weight_per_meter_kg' => 4.250,
                'is_cut_to_length' => true,
                'standard_length_mm' => 6000,
                'price_per_kg_eur' => 1.28,
                'stock_quantity_kg' => 4100,
                'is_featured' => true,
                'certifications_available' => ['2.2', '3.1'],
                'anarbeitung' => ['saw_cut', 'deburr', 'sandblast', 'galvanize'],
            ],
            // 15. Nahtloses Rohr S355J2H Ø60,3x4mm 6m (cut-to-length)
            [
                'category_id' => $cat('rundrohre'),
                'material_id' => $mat('S355J2H'),
                'form_id' => $form('rundrohr'),
                'sku' => 'NR-S355-60x4',
                'name' => 'Nahtloses Rohr S355J2H, Ø 60,3 × 4 mm',
                'short_description' => 'Nahtloses Stahlrohr, warmgefertigt',
                'long_description' => 'Nahtloses Rundrohr aus S355J2H nach EN 10210. Außendurchmesser 60,3 mm, Wanddicke 4 mm. Standardlänge 6.000 mm.',
                'dimensions' => ['outer_diameter_mm' => 60.3, 'wall_thickness_mm' => 4, 'length_mm' => 6000],
                'weight_per_meter_kg' => 5.550,
                'is_cut_to_length' => true,
                'standard_length_mm' => 6000,
                'price_per_kg_eur' => 1.45,
                'stock_quantity_kg' => 0,
                'has_restlaengen' => false,
                'certifications_available' => ['2.2', '3.1', '3.2'],
                'anarbeitung' => ['saw_cut', 'deburr', 'sandblast'],
            ],
            // 16. Präzisionsstahlrohr E235+N Ø20x2mm 6m (cut-to-length)
            [
                'category_id' => $cat('praezisionsstahlrohre'),
                'material_id' => $mat('E235+N'),
                'form_id' => $form('praezisionsrohr'),
                'sku' => 'PR-E235-20x2',
                'name' => 'Präzisionsstahlrohr E235+N, Ø 20 × 2 mm',
                'short_description' => 'Kaltgezogenes Präzisionsstahlrohr',
                'long_description' => 'Präzisionsstahlrohr kaltgezogen aus E235+N nach EN 10305-1. Außendurchmesser 20 mm, Wanddicke 2 mm. Enge Toleranzen, glatte Oberfläche.',
                'dimensions' => ['outer_diameter_mm' => 20, 'wall_thickness_mm' => 2, 'length_mm' => 6000],
                'weight_per_meter_kg' => 0.888,
                'is_cut_to_length' => true,
                'standard_length_mm' => 6000,
                'price_per_kg_eur' => 2.10,
                'stock_quantity_kg' => 1500,
                'certifications_available' => ['2.2', '3.1'],
                'anarbeitung' => ['saw_cut', 'deburr'],
            ],
            // 17. Feinblech DC01 2000x1000x1,5mm (per piece)
            [
                'category_id' => $cat('feinbleche'),
                'material_id' => $mat('DC01'),
                'form_id' => $form('blech'),
                'sku' => 'FB-DC01-1500x1000x1.5',
                'name' => 'Feinblech kaltgewalzt DC01, 2000 × 1000 × 1,5 mm',
                'short_description' => 'Kaltgewalztes Feinblech, Oberfläche matt',
                'long_description' => 'Feinblech kaltgewalzt aus DC01 nach EN 10130. 2000 × 1000 mm, Dicke 1,5 mm. Geeignet für Umformzwecke, Biegearbeiten und allgemeinen Blechbau.',
                'dimensions' => ['width_mm' => 2000, 'length_mm' => 1000, 'thickness_mm' => 1.5],
                'weight_per_piece_kg' => 23.55, // 2.0 * 1.0 * 0.0015 * 7850
                'is_cut_to_length' => false,
                'price_per_kg_eur' => 1.35,
                'stock_quantity_kg' => 5000,
                'certifications_available' => ['2.2', '3.1'],
                'anarbeitung' => ['sandblast', 'galvanize', 'prime'],
            ],
            // 18. Grobblech S355J2+N 2000x1000x10mm (per piece)
            [
                'category_id' => $cat('grobbleche'),
                'material_id' => $mat('S355J2+N'),
                'form_id' => $form('blech'),
                'sku' => 'GB-S355-2000x1000x10',
                'name' => 'Grobblech S355J2+N, 2000 × 1000 × 10 mm',
                'short_description' => 'Warmgewalztes Grobblech, normalgeglüht',
                'long_description' => 'Grobblech warmgewalzt und normalgeglüht aus S355J2+N nach EN 10025-2. 2000 × 1000 mm, Dicke 10 mm.',
                'dimensions' => ['width_mm' => 2000, 'length_mm' => 1000, 'thickness_mm' => 10],
                'weight_per_piece_kg' => 157.00, // 2.0 * 1.0 * 0.01 * 7850 (rounded)
                'is_cut_to_length' => false,
                'price_per_kg_eur' => 1.28,
                'stock_quantity_kg' => 7800,
                'is_featured' => true,
                'certifications_available' => ['2.2', '3.1', '3.2'],
                'anarbeitung' => ['sandblast', 'prime'],
            ],
            // 19. Tränenblech S235JR 2000x1000x3/5mm (per piece)
            [
                'category_id' => $cat('traenenbleche'),
                'material_id' => $mat('S235JR'),
                'form_id' => $form('traenenblech'),
                'sku' => 'TB-S235-2000x1000x3',
                'name' => 'Tränenblech S235JR, 2000 × 1000 × 3/5 mm',
                'short_description' => 'Riffelblech mit Tränenprägung, rutschfest',
                'long_description' => 'Tränenblech (Riffelblech) aus S235JR nach EN 10025-2 / EN 10051. 2000 × 1000 mm, Grunddicke 3 mm, Gesamtdicke ca. 5 mm inkl. Tränen. Rutschhemmend, ideal für Treppenstufen und Bodenbeläge.',
                'dimensions' => ['width_mm' => 2000, 'length_mm' => 1000, 'thickness_mm' => 3],
                'weight_per_piece_kg' => 55.00, // approx. with pattern
                'is_cut_to_length' => false,
                'price_per_kg_eur' => 1.35,
                'stock_quantity_kg' => 3200,
                'certifications_available' => ['2.2'],
                'anarbeitung' => ['galvanize', 'prime'],
            ],
            // 20. Kesselblech P265GH 2000x1000x8mm (per piece)
            [
                'category_id' => $cat('sonderbleche'),
                'material_id' => $mat('P265GH'),
                'form_id' => $form('blech'),
                'sku' => 'KB-P265-2000x1000x8',
                'name' => 'Kesselblech P265GH, 2000 × 1000 × 8 mm',
                'short_description' => 'Druckbehälterstahl, warmfest',
                'long_description' => 'Kesselblech aus P265GH nach EN 10028-2. 2000 × 1000 mm, Dicke 8 mm. Für Druckbehälter und Apparatebau zugelassen.',
                'dimensions' => ['width_mm' => 2000, 'length_mm' => 1000, 'thickness_mm' => 8],
                'weight_per_piece_kg' => 125.60, // 2.0 * 1.0 * 0.008 * 7850
                'is_cut_to_length' => false,
                'price_per_kg_eur' => 1.55,
                'stock_quantity_kg' => 0,
                'certifications_available' => ['2.2', '3.1', '3.2'],
                'anarbeitung' => ['sandblast', 'prime'],
            ],
            // 21. Feuerverzinkter Flachstahl S235JR+Z275 40x5mm 6m
            [
                'category_id' => $cat('flachstahl'),
                'material_id' => $mat('S235JR+Z275'),
                'form_id' => $form('flachstahl'),
                'sku' => 'FS-Z275-40x5',
                'name' => 'Feuerverzinkter Flachstahl S235JR+Z275, 40 × 5 mm',
                'short_description' => 'Feuerverzinkter Flachstahl, korrosionsgeschützt',
                'long_description' => 'Flachstahl feuerverzinkt aus S235JR mit Z275 Zinkauflage nach EN 10346. 40 × 5 mm, Standardlänge 6.000 mm. Direkt korrosionsgeschützt, keine Nachbehandlung nötig.',
                'dimensions' => ['width_mm' => 40, 'thickness_mm' => 5, 'length_mm' => 6000],
                'weight_per_meter_kg' => 1.570, // 0.040 * 0.005 * 7850
                'is_cut_to_length' => true,
                'standard_length_mm' => 6000,
                'price_per_kg_eur' => 1.65,
                'stock_quantity_kg' => 1800,
                'certifications_available' => ['2.2', '3.1'],
                'anarbeitung' => ['saw_cut', 'deburr'],
            ],
            // 22. Edelstahl Rundstahl 1.4301 Ø25mm 3m
            [
                'category_id' => $cat('edelstahl-rundstahl'),
                'material_id' => $mat('1.4301'),
                'form_id' => $form('rundstahl'),
                'sku' => 'ER-4301-D25',
                'name' => 'Edelstahl Rundstahl 1.4301 (V2A), Ø 25 mm',
                'short_description' => 'Austenitischer Edelstahl, blank gezogen',
                'long_description' => 'Rundstahl aus 1.4301 (V2A / AISI 304) nach EN 10088-3. Ø 25 mm, Standardlänge 3.000 mm. Korrosionsbeständig, lebensmittelgeeignet.',
                'dimensions' => ['diameter_mm' => 25, 'length_mm' => 3000],
                'weight_per_meter_kg' => 3.878, // π/4 * 0.025² * 7900
                'is_cut_to_length' => true,
                'standard_length_mm' => 3000,
                'price_per_kg_eur' => 4.20,
                'stock_quantity_kg' => 1200,
                'is_featured' => true,
                'certifications_available' => ['2.2', '3.1', '3.2'],
                'anarbeitung' => ['saw_cut', 'deburr'],
            ],
            // 23. Edelstahl Flachstahl 1.4571 50x10mm 3m
            [
                'category_id' => $cat('edelstahl-flachstahl'),
                'material_id' => $mat('1.4571'),
                'form_id' => $form('flachstahl'),
                'sku' => 'EF-4571-50x10',
                'name' => 'Edelstahl Flachstahl 1.4571 (V4A), 50 × 10 mm',
                'short_description' => 'Säurebeständiger Edelstahl, molybdänlegiert',
                'long_description' => 'Flachstahl aus 1.4571 (V4A / AISI 316Ti) nach EN 10088-3. 50 × 10 mm, Standardlänge 3.000 mm. Säure- und chloridbeständig.',
                'dimensions' => ['width_mm' => 50, 'thickness_mm' => 10, 'length_mm' => 3000],
                'weight_per_meter_kg' => 3.990, // 0.050 * 0.010 * 7980
                'is_cut_to_length' => true,
                'standard_length_mm' => 3000,
                'price_per_kg_eur' => 5.80,
                'stock_quantity_kg' => 0,
                'certifications_available' => ['2.2', '3.1', '3.2'],
                'anarbeitung' => ['saw_cut', 'deburr'],
            ],
            // 24. Edelstahlblech 1.4301 2000x1000x2mm IIID (per piece)
            [
                'category_id' => $cat('edelstahlbleche'),
                'material_id' => $mat('1.4301'),
                'form_id' => $form('blech'),
                'sku' => 'EB-4301-2000x1000x2',
                'name' => 'Edelstahlblech 1.4301, 2000 × 1000 × 2 mm, Oberfläche IIID',
                'short_description' => 'Edelstahlblech kaltgewalzt, gebürstete Oberfläche',
                'long_description' => 'Edelstahlblech aus 1.4301 (V2A) nach EN 10088-2. 2000 × 1000 mm, Dicke 2 mm. Oberfläche IIID (geschliffen/gebürstet). Für Fassaden, Verkleidungen und dekorative Anwendungen.',
                'dimensions' => ['width_mm' => 2000, 'length_mm' => 1000, 'thickness_mm' => 2],
                'weight_per_piece_kg' => 31.60, // 2.0 * 1.0 * 0.002 * 7900
                'is_cut_to_length' => false,
                'price_per_kg_eur' => 4.50,
                'stock_quantity_kg' => 1600,
                'certifications_available' => ['2.2', '3.1'],
                'anarbeitung' => ['sandblast'],
            ],
            // 25. Edelstahl Winkelstahl 1.4301 30x30x3mm 3m (fixed length)
            [
                'category_id' => $cat('edelstahl-profile'),
                'material_id' => $mat('1.4301'),
                'form_id' => $form('winkelstahl'),
                'sku' => 'EW-4301-30x30x3',
                'name' => 'Edelstahl Winkelstahl 1.4301, 30 × 30 × 3 mm, 3 m',
                'short_description' => 'Edelstahl-Winkelprofil, gleichschenklig',
                'long_description' => 'Winkelstahl aus 1.4301 (V2A) nach EN 10088-3. 30 × 30 × 3 mm, Festlänge 3.000 mm.',
                'dimensions' => ['width_mm' => 30, 'height_mm' => 30, 'thickness_mm' => 3, 'length_mm' => 3000],
                'weight_per_piece_kg' => 4.05, // 1.35 kg/m * 3
                'weight_per_meter_kg' => 1.350,
                'is_cut_to_length' => false,
                'price_per_kg_eur' => 4.80,
                'stock_quantity_kg' => 0,
                'certifications_available' => ['2.2', '3.1'],
                'anarbeitung' => ['saw_cut', 'deburr'],
            ],
            // 26. Alu-Rechteckrohr EN AW-6060 40x40x2mm 6m
            [
                'category_id' => $cat('aluminium'),
                'material_id' => $mat('EN AW-6060'),
                'form_id' => $form('quadratrohr'),
                'sku' => 'AR-6060-40x40x2',
                'name' => 'Alu-Rechteckrohr EN AW-6060, 40 × 40 × 2 mm',
                'short_description' => 'Aluminium-Vierkantrohr, eloxierfähig',
                'long_description' => 'Alu-Rechteckrohr (quadratisch) aus EN AW-6060 T66 nach EN 755-2. 40 × 40 mm, Wanddicke 2 mm. Standardlänge 6.000 mm.',
                'dimensions' => ['width_mm' => 40, 'wall_thickness_mm' => 2, 'length_mm' => 6000],
                'weight_per_meter_kg' => 0.811, // approx
                'is_cut_to_length' => true,
                'standard_length_mm' => 6000,
                'price_per_kg_eur' => 6.50,
                'stock_quantity_kg' => 0,
                'certifications_available' => ['2.2', '3.1'],
                'anarbeitung' => ['saw_cut', 'deburr'],
            ],
            // 27. Alu-Blech EN AW-5754 2000x1000x3mm (per piece)
            [
                'category_id' => $cat('aluminium'),
                'material_id' => $mat('EN AW-5754'),
                'form_id' => $form('blech'),
                'sku' => 'AB-5754-2000x1000x3',
                'name' => 'Alu-Blech EN AW-5754, 2000 × 1000 × 3 mm',
                'short_description' => 'Aluminium-Blech, seewasserbeständig',
                'long_description' => 'Aluminium-Blech aus EN AW-5754 H22 nach EN 485-2. 2000 × 1000 mm, Dicke 3 mm. Seewasserbeständig, gut schweißbar.',
                'dimensions' => ['width_mm' => 2000, 'length_mm' => 1000, 'thickness_mm' => 3],
                'weight_per_piece_kg' => 15.96, // 2.0 * 1.0 * 0.003 * 2660
                'is_cut_to_length' => false,
                'price_per_kg_eur' => 7.20,
                'stock_quantity_kg' => 1200,
                'certifications_available' => ['2.2', '3.1'],
                'anarbeitung' => ['sandblast'],
            ],
            // 28. Messing Rundstahl CW614N Ø20mm 3m
            [
                'category_id' => $cat('messing'),
                'material_id' => $mat('CW614N'),
                'form_id' => $form('rundstahl'),
                'sku' => 'MR-CW614-D20',
                'name' => 'Messing Rundstahl CW614N, Ø 20 mm',
                'short_description' => 'Automatenmessing, gut zerspanbar',
                'long_description' => 'Messing-Rundstahl aus CW614N (Ms58) nach EN 12164. Ø 20 mm, Standardlänge 3.000 mm. Hervorragende Zerspanbarkeit, ideal für Drehteile.',
                'dimensions' => ['diameter_mm' => 20, 'length_mm' => 3000],
                'weight_per_meter_kg' => 2.661, // π/4 * 0.020² * 8470
                'is_cut_to_length' => true,
                'standard_length_mm' => 3000,
                'price_per_kg_eur' => 8.50,
                'stock_quantity_kg' => 0,
                'has_restlaengen' => false,
                'certifications_available' => ['2.2', '3.1'],
                'anarbeitung' => ['saw_cut', 'deburr'],
            ],
            // 29. Kupfer-Blech Cu-DHP 1000x500x2mm (per piece)
            [
                'category_id' => $cat('kupfer'),
                'material_id' => $mat('Cu-DHP'),
                'form_id' => $form('blech'),
                'sku' => 'CU-DHP-1000x500x2',
                'name' => 'Kupfer-Blech Cu-DHP, 1000 × 500 × 2 mm',
                'short_description' => 'Kupferblech, hohe Leitfähigkeit',
                'long_description' => 'Kupfer-Blech aus Cu-DHP (CW024A) nach EN 1652. 1000 × 500 mm, Dicke 2 mm. Hohe elektrische und thermische Leitfähigkeit.',
                'dimensions' => ['width_mm' => 1000, 'length_mm' => 500, 'thickness_mm' => 2],
                'weight_per_piece_kg' => 8.90, // 1.0 * 0.5 * 0.002 * 8900
                'is_cut_to_length' => false,
                'price_per_kg_eur' => 12.00,
                'stock_quantity_kg' => 0,
                'certifications_available' => ['2.2', '3.1'],
                'anarbeitung' => [],
            ],
            // 30. Federstahl 51CrV4 30x5mm 2m
            [
                'category_id' => $cat('ne-sonstiges'),
                'material_id' => $mat('51CrV4'),
                'form_id' => $form('flachstahl'),
                'sku' => 'FD-51CrV4-30x5',
                'name' => 'Federstahl 51CrV4, 30 × 5 mm',
                'short_description' => 'Vergüteter Federstahl, hohe Elastizität',
                'long_description' => 'Federstahl-Flachstab aus 51CrV4 nach EN 10089. 30 × 5 mm, Standardlänge 2.000 mm. Vergütet, hohe Dauerfestigkeit und Elastizität.',
                'dimensions' => ['width_mm' => 30, 'thickness_mm' => 5, 'length_mm' => 2000],
                'weight_per_meter_kg' => 1.178, // 0.030 * 0.005 * 7850
                'is_cut_to_length' => true,
                'standard_length_mm' => 2000,
                'price_per_kg_eur' => 3.80,
                'stock_quantity_kg' => 0,
                'certifications_available' => ['2.2', '3.1', '3.2'],
                'anarbeitung' => ['saw_cut', 'deburr'],
            ],
        ];

        foreach ($products as $data) {
            $anarbeitungCodes = $data['anarbeitung'] ?? [];
            unset($data['anarbeitung']);

            $product = Product::create($data);

            if (!empty($anarbeitungCodes)) {
                $optionIds = AnarbeitungOption::whereIn('code', $anarbeitungCodes)->pluck('id');
                $product->anarbeitungOptions()->attach($optionIds);
            }
        }

        // Assign supplier names to Bestellware products
        $supplierMap = [
            'RS-S355-D50'         => 'Saarstahl AG',
            'HEB-200-S355'        => 'ArcelorMittal Bremen',
            'NR-S355-60x4'        => 'Salzgitter Mannesmann',
            'KB-P265-2000x1000x8' => 'Dillinger Hütte',
            'EF-4571-50x10'       => 'Outokumpu Nirosta',
            'EW-4301-30x30x3'     => 'Outokumpu Nirosta',
            'AR-6060-40x40x2'     => 'Hydro Aluminium',
            'MR-CW614-D20'        => 'Wieland Werke AG',
            'CU-DHP-1000x500x2'   => 'KME Germany GmbH',
            'FD-51CrV4-30x5'      => 'Deutsche Edelstahlwerke',
        ];

        foreach ($supplierMap as $sku => $supplier) {
            Product::where('sku', $sku)->update(['supplier_name' => $supplier]);
        }

        // Mark some products as sourced from partner network (Sortimentserweiterung)
        $partnerProducts = [
            'EF-4571-50x10'       => 'Stahl-Center Süd GmbH, Ulm',
            'EW-4301-30x30x3'     => 'Stahl-Center Süd GmbH, Ulm',
            'AR-6060-40x40x2'     => 'NE-Metall Partner AG, Friedrichshafen',
            'AB-5754-2000x1000x3' => 'NE-Metall Partner AG, Friedrichshafen',
            'MR-CW614-D20'        => 'Buntmetall Handel GmbH, Stuttgart',
            'CU-DHP-1000x500x2'   => 'Buntmetall Handel GmbH, Stuttgart',
            'FD-51CrV4-30x5'      => 'Spezialstahl Vertrieb, München',
        ];

        foreach ($partnerProducts as $sku => $source) {
            Product::where('sku', $sku)->update([
                'is_partner_network' => true,
                'partner_source' => $source,
            ]);
        }

        // Set ERP sync timestamps and availability for sale
        // All products come from ERP; most are available, some are held back
        Product::query()->update([
            'erp_synced_at' => now()->subHours(rand(1, 8)),
        ]);

        // Set ERP cost prices (slightly varied from the 82% default)
        foreach (Product::all() as $p) {
            $factor = 0.80 + (crc32($p->sku) % 8) / 100; // 0.80-0.87
            $p->update([
                'erp_price_per_kg' => round($p->price_per_kg_eur * $factor, 4),
                'erp_synced_at' => now()->subHours(rand(1, 8)),
            ]);
        }

        // Mark some Bestellware products as not available for sale
        // (simulating products in ERP but not yet released for the shop)
        $notForSale = ['CU-DHP-1000x500x2', 'FD-51CrV4-30x5', 'MR-CW614-D20'];
        Product::whereIn('sku', $notForSale)->update(['is_available_for_sale' => false]);
    }
}
