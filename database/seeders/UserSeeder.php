<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'customer_number' => 'K-10234',
                'name' => 'Franz Kowalski',
                'email' => 'f.kowalski@schlosserei-bergmann.de',
                'password' => 'password',
                'role' => 'admin',
            ],
            [
                'customer_number' => 'K-10456',
                'name' => 'Maria Bruckner',
                'email' => 'm.bruckner@metallbau-bruckner.de',
                'password' => 'password',
                'role' => 'buyer',
            ],
            [
                'customer_number' => 'K-10589',
                'name' => 'Hans Dietrich',
                'email' => 'h.dietrich@konstruktion-dietrich.de',
                'password' => 'password',
                'role' => 'buyer',
            ],
            [
                'customer_number' => 'K-10712',
                'name' => 'Andreas Riedl',
                'email' => 'a.riedl@anlagenbau-riedl.de',
                'password' => 'password',
                'role' => 'buyer',
            ],
            [
                'customer_number' => 'K-10834',
                'name' => 'Klaus Schlosser',
                'email' => 'k.schlosser@schlosser-partner.de',
                'password' => 'password',
                'role' => 'buyer',
            ],
        ];

        foreach ($users as $data) {
            $customer = Customer::where('customer_number', $data['customer_number'])->first();
            User::create([
                'customer_id' => $customer->id,
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'role' => $data['role'],
            ]);
        }
    }
}
