<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spare_parts', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->string('name');
            $table->enum('condition', ['neuve', 'occasion']);
            $table->decimal('price_usd', 12, 2);
            $table->decimal('price_fcfa', 14, 2);
            $table->unsignedInteger('stock_quantity')->default(0);
            $table->boolean('online_sale_enabled')->default(true);
            $table->timestamps();

            $table->index('online_sale_enabled');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spare_parts');
    }
};
