<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->morphs('payable'); // reservation ou order
            $table->string('provider'); // agrégateur multi-moyens
            $table->string('method')->nullable(); // carte, mobile money, etc.
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3);
            $table->string('transaction_reference')->nullable();
            $table->enum('status', ['en_attente', 'reussi', 'echoue', 'rembourse'])->default('en_attente');
            $table->json('gateway_payload')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
