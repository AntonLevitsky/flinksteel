<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wholesaler_id')->constrained()->cascadeOnDelete();
            $table->string('company_name');
            $table->string('customer_number')->unique();
            $table->string('street');
            $table->string('postal_code', 10);
            $table->string('city');
            $table->string('vat_id')->nullable();
            $table->decimal('credit_limit_eur', 10, 2)->default(50000);
            $table->integer('payment_terms_days')->default(30);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
