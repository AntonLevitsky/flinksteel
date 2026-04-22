<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->string('grade');
            $table->string('standard')->nullable();
            $table->text('description')->nullable();
            $table->decimal('density_kg_per_m3', 8, 1)->default(7850);
            $table->boolean('has_alloy_surcharge')->default(false);
            $table->boolean('is_stainless')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};
