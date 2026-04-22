<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Wholesaler;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $wholesaler = Wholesaler::first();

        $customers = [
            [
                'company_name' => 'Schlosserei Bergmann GmbH',
                'customer_number' => 'K-10234',
                'street' => 'Industriestraße 7',
                'postal_code' => '88250',
                'city' => 'Weingarten',
                'vat_id' => 'DE123456789',
                'credit_limit_eur' => 75000,
                'payment_terms_days' => 30,
                'price_multiplier' => 0.94,
                'price_tier' => 'premium',
            ],
            [
                'company_name' => 'Metallbau Bruckner KG',
                'customer_number' => 'K-10456',
                'street' => 'Römerweg 22',
                'postal_code' => '88212',
                'city' => 'Ravensburg',
                'vat_id' => 'DE234567890',
                'credit_limit_eur' => 50000,
                'payment_terms_days' => 14,
                'price_multiplier' => 1.000,
                'price_tier' => 'standard',
            ],
            [
                'company_name' => 'Konstruktion Dietrich GmbH',
                'customer_number' => 'K-10589',
                'street' => 'Lindauer Str. 45',
                'postal_code' => '88069',
                'city' => 'Tettnang',
                'vat_id' => 'DE345678901',
                'credit_limit_eur' => 60000,
                'payment_terms_days' => 30,
                'price_multiplier' => 0.97,
                'price_tier' => 'standard',
            ],
            [
                'company_name' => 'Anlagenbau Riedl GmbH',
                'customer_number' => 'K-10712',
                'street' => 'Industriepark 3',
                'postal_code' => '88074',
                'city' => 'Meckenbeuren',
                'vat_id' => 'DE456789012',
                'credit_limit_eur' => 100000,
                'payment_terms_days' => 45,
                'price_multiplier' => 0.90,
                'price_tier' => 'vip',
            ],
            [
                'company_name' => 'Schlosser & Partner AG',
                'customer_number' => 'K-10834',
                'street' => 'Am Bach 18',
                'postal_code' => '88079',
                'city' => 'Kressbronn',
                'vat_id' => 'DE567890123',
                'credit_limit_eur' => 80000,
                'payment_terms_days' => 30,
                'price_multiplier' => 0.95,
                'price_tier' => 'premium',
            ],
        ];

        foreach ($customers as $data) {
            Customer::create(array_merge($data, ['wholesaler_id' => $wholesaler->id]));
        }
    }
}
