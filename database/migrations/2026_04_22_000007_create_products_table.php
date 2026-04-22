<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('material_id')->constrained()->cascadeOnDelete();
            $table->foreignId('form_id')->constrained()->cascadeOnDelete();
            $table->string('sku')->unique();
            $table->string('name');
            $table->text('short_description')->nullable();
            $table->text('long_description')->nullable();
            $table->json('dimensions')->nullable();
            $table->decimal('weight_per_piece_kg', 10, 3)->nullable();
            $table->decimal('weight_per_meter_kg', 10, 3)->nullable();
            $table->boolean('is_cut_to_length')->default(false);
            $table->integer('standard_length_mm')->nullable();
            $table->decimal('price_per_kg_eur', 8, 2);
            $table->decimal('stock_quantity_kg', 12, 2)->default(0);
            $table->boolean('has_restlaengen')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->json('certifications_available')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
