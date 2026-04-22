<?php

namespace Database\Seeders;

use App\Models\Certificate;
use Illuminate\Database\Seeder;

class CertificateSeeder extends Seeder
{
    public function run(): void
    {
        $certificates = [
            ['code' => '2.2', 'name_de' => 'Werkszeugnis 2.2', 'description_de' => 'Bescheinigung über Übereinstimmung mit der Bestellung, vom Hersteller ausgestellt.', 'surcharge_eur' => 0],
            ['code' => '3.1', 'name_de' => 'Abnahmeprüfzeugnis 3.1', 'description_de' => 'Zeugnis mit Prüfergebnissen, vom Hersteller ausgestellt und vom unabhängigen Prüfer bestätigt.', 'surcharge_eur' => 15.00],
            ['code' => '3.2', 'name_de' => 'Abnahmeprüfzeugnis 3.2', 'description_de' => 'Zeugnis mit Prüfergebnissen, von Hersteller und unabhängigem Sachverständigen ausgestellt.', 'surcharge_eur' => 45.00],
        ];

        foreach ($certificates as $data) {
            Certificate::create($data);
        }
    }
}
