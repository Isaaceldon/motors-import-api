<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')->constrained()->restrictOnDelete();
            $table->string('model');
            $table->string('version')->nullable();
            $table->unsignedSmallInteger('year');
            $table->enum('condition', ['neuf', 'occasion']);
            $table->enum('status', ['disponible', 'reserve', 'vendu'])->default('disponible');

            $table->decimal('price_usd', 12, 2);
            $table->decimal('price_fcfa', 14, 2);
            $table->unsignedTinyInteger('deposit_percentage_snapshot')->nullable();

            $table->unsignedInteger('mileage')->nullable();
            $table->string('fuel_type')->nullable();
            $table->string('transmission')->nullable();
            $table->string('engine')->nullable();
            $table->string('color')->nullable();
            $table->unsignedTinyInteger('doors')->nullable();
            $table->unsignedTinyInteger('seats')->nullable();
            $table->string('body_style')->nullable();
            $table->string('vin')->nullable();

            $table->boolean('is_featured')->default(false);
            $table->timestamp('sold_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'condition']);
            $table->index(['brand_id', 'model']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
