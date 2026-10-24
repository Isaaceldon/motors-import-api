<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->string('preferred_locale', 5)->default('fr')->after('phone');
            $table->string('preferred_currency', 3)->default('USD')->after('preferred_locale');
            $table->enum('role', ['client', 'admin'])->default('client')->after('preferred_currency');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'preferred_locale', 'preferred_currency', 'role']);
        });
    }
};
