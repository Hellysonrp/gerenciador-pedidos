<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\AsStringable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    // usa soft delete, ou seja, não é realmente deletado do banco e é só marcado como deletado
    // no caso dos produtos, o soft delete permite excluir produtos sem precisar excluir os pedidos que usaram tais produtos
    use SoftDeletes;
    // possui uma 'factory' para criar dados com o comando db:seed
    use HasFactory;

    // campos preenchíveis
    protected $fillable = [
        'name',
        'stock',
        'price',
        'photo',
    ];

    // tipos de dados dos campos
    protected $casts = [
        'stock' => 'double',
        'price' => 'double',
        'photo' => AsStringable::class, // não sei se é necessário, mas tá funcionando e agora não vou remover (tinha adicionado quando a coluna era binária)
    ];
}
