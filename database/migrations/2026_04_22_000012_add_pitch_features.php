<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Customer-specific pricing multiplier
        Schema::table('customers', function (Blueprint $table) {
            $table->decimal('price_multiplier', 5, 3)->default(1.000)->after('payment_terms_days');
            $table->string('price_tier')->default('standard')->after('price_multiplier'); // standard, premium, vip
        });

        // Supplier sourcing and partner network for products
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('is_partner_network')->default(false)->after('is_active');
            $table->string('partner_source')->nullable()->after('is_partner_network'); // e.g. "Stahl-Center Süd"
            $table->string('supplier_name')->nullable()->after('partner_source'); // for Bestellware
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['price_multiplier', 'price_tier']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['is_partner_network', 'partner_source', 'supplier_name']);
        });
    }
};
