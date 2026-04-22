<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class SampleOrderSeeder extends Seeder
{
    public function run(): void
    {
        $franz = User::where('email', 'f.kowalski@schlosserei-bergmann.de')->first();
        $customer = $franz->customer;
        $products = Product::with('material')->get();

        // Order 1: Delivered (6 months ago)
        $order1 = Order::create([
            'customer_id' => $customer->id,
            'user_id' => $franz->id,
            'order_number' => 'B-202600087',
            'status' => 'versandt',
            'subtotal_eur' => 1247.50,
            'anarbeitung_total_eur' => 42.00,
            'certificate_total_eur' => 15.00,
            'shipping_eur' => 0,
            'total_eur' => 1552.36,
            'placed_at' => now()->subMonths(6),
            'requested_delivery_date' => now()->subMonths(6)->addDays(5),
            'delivery_street' => $customer->street,
            'delivery_postal_code' => $customer->postal_code,
            'delivery_city' => $customer->city,
        ]);

        $p1 = $products->where('sku', 'RS-S235-D20')->first();
        $p2 = $products->where('sku', 'FS-S235-40x10')->first();
        $p3 = $products->where('sku', 'IPE-200-S235')->first();

        OrderItem::create([
            'order_id' => $order1->id,
            'product_id' => $p1->id,
            'product_name' => $p1->name,
            'product_sku' => $p1->sku,
            'material_grade' => $p1->material->grade,
            'quantity' => 10,
            'length_mm' => 3000,
            'anarbeitung' => ['saw_cut', 'deburr'],
            'certificate_code' => '3.1',
            'unit_price_eur' => 9.24,
            'anarbeitung_cost_eur' => 47.00,
            'certificate_cost_eur' => 15.00,
            'line_total_eur' => 154.35,
            'weight_kg' => 73.98,
        ]);

        OrderItem::create([
            'order_id' => $order1->id,
            'product_id' => $p2->id,
            'product_name' => $p2->name,
            'product_sku' => $p2->sku,
            'material_grade' => $p2->material->grade,
            'quantity' => 20,
            'length_mm' => 2000,
            'anarbeitung' => ['saw_cut'],
            'certificate_code' => '2.2',
            'unit_price_eur' => 7.54,
            'anarbeitung_cost_eur' => 70.00,
            'certificate_cost_eur' => 0,
            'line_total_eur' => 220.72,
            'weight_kg' => 125.60,
        ]);

        OrderItem::create([
            'order_id' => $order1->id,
            'product_id' => $p3->id,
            'product_name' => $p3->name,
            'product_sku' => $p3->sku,
            'material_grade' => $p3->material->grade,
            'quantity' => 3,
            'length_mm' => null,
            'anarbeitung' => [],
            'certificate_code' => '2.2',
            'unit_price_eur' => 317.18,
            'anarbeitung_cost_eur' => 0,
            'certificate_cost_eur' => 0,
            'line_total_eur' => 951.55,
            'weight_kg' => 806.40,
        ]);

        // Recalculate order 1 totals
        $order1->update([
            'subtotal_eur' => 1326.62,
            'anarbeitung_total_eur' => 117.00,
            'certificate_total_eur' => 15.00,
            'shipping_eur' => 0,
            'total_eur' => round((1326.62 + 117.00 + 15.00) * 1.19, 2),
        ]);

        // Order 2: In Bearbeitung (2 months ago)
        $order2 = Order::create([
            'customer_id' => $customer->id,
            'user_id' => $franz->id,
            'order_number' => 'B-202600142',
            'status' => 'in_bearbeitung',
            'subtotal_eur' => 0,
            'anarbeitung_total_eur' => 0,
            'certificate_total_eur' => 0,
            'shipping_eur' => 49.00,
            'total_eur' => 0,
            'placed_at' => now()->subMonths(2),
            'requested_delivery_date' => now()->subMonths(2)->addDays(7),
            'delivery_street' => $customer->street,
            'delivery_postal_code' => $customer->postal_code,
            'delivery_city' => $customer->city,
        ]);

        $p4 = $products->where('sku', 'RR-S235-80x40x4')->first();
        $p5 = $products->where('sku', 'QR-S235-50x50x3')->first();
        $p6 = $products->where('sku', 'ER-4301-D25')->first();
        $p7 = $products->where('sku', 'WS-S235-50x50x5')->first();

        OrderItem::create([
            'order_id' => $order2->id,
            'product_id' => $p4->id,
            'product_name' => $p4->name,
            'product_sku' => $p4->sku,
            'material_grade' => $p4->material->grade,
            'quantity' => 5,
            'length_mm' => 4000,
            'anarbeitung' => ['saw_cut', 'deburr'],
            'certificate_code' => '2.2',
            'unit_price_eur' => 28.34,
            'anarbeitung_cost_eur' => 23.50,
            'certificate_cost_eur' => 0,
            'line_total_eur' => 165.22,
            'weight_kg' => 109.00,
        ]);

        OrderItem::create([
            'order_id' => $order2->id,
            'product_id' => $p5->id,
            'product_name' => $p5->name,
            'product_sku' => $p5->sku,
            'material_grade' => $p5->material->grade,
            'quantity' => 8,
            'length_mm' => 2500,
            'anarbeitung' => ['saw_cut'],
            'certificate_code' => '2.2',
            'unit_price_eur' => 13.60,
            'anarbeitung_cost_eur' => 28.00,
            'certificate_cost_eur' => 0,
            'line_total_eur' => 136.80,
            'weight_kg' => 85.00,
        ]);

        OrderItem::create([
            'order_id' => $order2->id,
            'product_id' => $p6->id,
            'product_name' => $p6->name,
            'product_sku' => $p6->sku,
            'material_grade' => $p6->material->grade,
            'quantity' => 3,
            'length_mm' => 1500,
            'anarbeitung' => ['saw_cut', 'deburr'],
            'certificate_code' => '3.1',
            'unit_price_eur' => 24.43,
            'anarbeitung_cost_eur' => 14.10,
            'certificate_cost_eur' => 15.00,
            'line_total_eur' => 102.45,
            'weight_kg' => 17.45,
        ]);

        OrderItem::create([
            'order_id' => $order2->id,
            'product_id' => $p7->id,
            'product_name' => $p7->name,
            'product_sku' => $p7->sku,
            'material_grade' => $p7->material->grade,
            'quantity' => 4,
            'length_mm' => null,
            'anarbeitung' => [],
            'certificate_code' => '2.2',
            'unit_price_eur' => 27.30,
            'anarbeitung_cost_eur' => 0,
            'certificate_cost_eur' => 0,
            'line_total_eur' => 109.22,
            'weight_kg' => 89.52,
        ]);

        $sub2 = 165.22 + 136.80 + 102.45 + 109.22;
        $order2->update([
            'subtotal_eur' => $sub2,
            'anarbeitung_total_eur' => 65.60,
            'certificate_total_eur' => 15.00,
            'shipping_eur' => 49.00,
            'total_eur' => round(($sub2 + 49.00) * 1.19, 2),
        ]);

        // Order 3: Bestätigt (1 week ago)
        $order3 = Order::create([
            'customer_id' => $customer->id,
            'user_id' => $franz->id,
            'order_number' => 'B-202600198',
            'status' => 'bestaetigt',
            'subtotal_eur' => 0,
            'anarbeitung_total_eur' => 0,
            'certificate_total_eur' => 0,
            'shipping_eur' => 0,
            'total_eur' => 0,
            'placed_at' => now()->subDays(7),
            'requested_delivery_date' => now()->addDays(3),
            'delivery_street' => $customer->street,
            'delivery_postal_code' => $customer->postal_code,
            'delivery_city' => $customer->city,
        ]);

        $p8 = $products->where('sku', 'GB-S355-2000x1000x10')->first();
        $p9 = $products->where('sku', 'EF-4571-50x10')->first();

        OrderItem::create([
            'order_id' => $order3->id,
            'product_id' => $p8->id,
            'product_name' => $p8->name,
            'product_sku' => $p8->sku,
            'material_grade' => $p8->material->grade,
            'quantity' => 5,
            'length_mm' => null,
            'anarbeitung' => ['sandblast', 'prime'],
            'certificate_code' => '3.1',
            'unit_price_eur' => 200.96,
            'anarbeitung_cost_eur' => 219.80,
            'certificate_cost_eur' => 15.00,
            'line_total_eur' => 1239.60,
            'weight_kg' => 785.00,
        ]);

        OrderItem::create([
            'order_id' => $order3->id,
            'product_id' => $p9->id,
            'product_name' => $p9->name,
            'product_sku' => $p9->sku,
            'material_grade' => $p9->material->grade,
            'quantity' => 4,
            'length_mm' => 2000,
            'anarbeitung' => ['saw_cut'],
            'certificate_code' => '3.1',
            'unit_price_eur' => 46.28,
            'anarbeitung_cost_eur' => 14.00,
            'certificate_cost_eur' => 15.00,
            'line_total_eur' => 214.14,
            'weight_kg' => 31.92,
        ]);

        $sub3 = 1239.60 + 214.14;
        $order3->update([
            'subtotal_eur' => $sub3,
            'anarbeitung_total_eur' => 233.80,
            'certificate_total_eur' => 30.00,
            'shipping_eur' => 0,
            'total_eur' => round(($sub3) * 1.19, 2),
        ]);
    }
}
