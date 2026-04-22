<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('order_number')->unique();
            $table->string('status')->default('bestaetigt');
            $table->decimal('subtotal_eur', 10, 2)->default(0);
            $table->decimal('anarbeitung_total_eur', 10, 2)->default(0);
            $table->decimal('certificate_total_eur', 10, 2)->default(0);
            $table->decimal('shipping_eur', 10, 2)->default(0);
            $table->decimal('total_eur', 10, 2)->default(0);
            $table->timestamp('placed_at')->nullable();
            $table->date('requested_delivery_date')->nullable();
            $table->string('delivery_street')->nullable();
            $table->string('delivery_postal_code', 10)->nullable();
            $table->string('delivery_city')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('product_name');
            $table->string('product_sku');
            $table->string('material_grade');
            $table->integer('quantity');
            $table->integer('length_mm')->nullable();
            $table->json('anarbeitung')->nullable();
            $table->string('certificate_code')->nullable();
            $table->decimal('unit_price_eur', 10, 2)->default(0);
            $table->decimal('anarbeitung_cost_eur', 10, 2)->default(0);
            $table->decimal('certificate_cost_eur', 10, 2)->default(0);
            $table->decimal('line_total_eur', 10, 2)->default(0);
            $table->decimal('weight_kg', 10, 3)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
