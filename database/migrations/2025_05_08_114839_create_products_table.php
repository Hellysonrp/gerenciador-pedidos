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
        // cria a tabela de produtos
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 191)->index();
            $table->decimal('stock', 13, 5)->default(0);
            $table->decimal('price', 13, 5)->default(0);
            // $table->binary('photo')->nullable();
            $table->longText('photo')->nullable(); // estava tendo problemas com postgres e binários no eloquent; trocado pra texto e vai salvar base64
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
