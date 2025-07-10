<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Adiciona a coluna nota_fiscal_id na tabela orders
        // é um id de uma integração externa; não possui FK
        Schema::table('orders', function (Blueprint $table) {
            $table->bigInteger('nota_fiscal_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove a coluna nota_fiscal_id da tabela orders
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('nota_fiscal_id');
        });
    }
};
