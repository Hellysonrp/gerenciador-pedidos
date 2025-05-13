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
        // cria a tabela de produtos do pedido
        Schema::create('order_products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('order_id')->index()->constrained();
            $table->foreignId('product_id')->index()->constrained();
            $table->string('product_name', 191); // redundância de dados, para não depender do produto em si
            $table->decimal('original_price', 13, 5); // preço original, que pode ser usado para calcular desconto/acréscimo
            $table->decimal('unit_price', 13, 5); // preço unitário informado no formulário
            $table->decimal('quantity', 13, 5);
            $table->decimal('total', 13, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_products');
    }
};
