<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anarbeitung_options', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name_de');
            $table->decimal('price_per_cut_eur', 8, 2)->nullable();
            $table->decimal('price_per_kg_eur', 8, 2)->nullable();
            $table->timestamps();
        });

        Schema::create('product_anarbeitung', function (Blueprint $table) {
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('anarbeitung_option_id')->constrained('anarbeitung_options')->cascadeOnDelete();
            $table->primary(['product_id', 'anarbeitung_option_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_anarbeitung');
        Schema::dropIfExists('anarbeitung_options');
    }
};
