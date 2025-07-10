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
        // coluna para indicar o status EXTERNO da nota fiscal
        // não possui constrains
        // é um inteiro (enum)
        Schema::table('orders', function (Blueprint $table) {
            $table->integer('nota_fiscal_status')->nullable()->after('nota_fiscal_id');
        });
        // 0 = pendente
        // 1 = autorizada
        // 2 = cancelada
        // 3 = rejeitada
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // remove a coluna nota_fiscal_status da tabela orders
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('nota_fiscal_status');
        });
    }
};
