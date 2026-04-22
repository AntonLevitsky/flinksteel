<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wholesalers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('primary_color', 7)->default('#1e3a8a');
            $table->string('accent_color', 7)->default('#f97316');
            $table->string('address');
            $table->string('phone');
            $table->string('email');
            $table->integer('founded_year');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wholesalers');
    }
};
