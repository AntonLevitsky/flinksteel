<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('is_available_for_sale')->default(true)->after('is_active');
            $table->decimal('erp_price_per_kg', 10, 4)->nullable()->after('price_per_kg_eur');
            $table->timestamp('erp_synced_at')->nullable()->after('supplier_name');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['is_available_for_sale', 'erp_price_per_kg', 'erp_synced_at']);
        });
    }
};
