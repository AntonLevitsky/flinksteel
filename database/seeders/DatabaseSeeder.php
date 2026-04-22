<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            WholesalerSeeder::class,
            CustomerSeeder::class,
            UserSeeder::class,
            CategorySeeder::class,
            MaterialSeeder::class,
            FormSeeder::class,
            AnarbeitungSeeder::class,
            CertificateSeeder::class,
            ProductSeeder::class,
            SampleOrderSeeder::class,
        ]);
    }
}
