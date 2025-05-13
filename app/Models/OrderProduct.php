<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderProduct extends Model
{
    // não usa soft delete
    // ao excluir um pedido, somente o cabeçalho do pedido é marcado como deletado e os produtos são mantidos
    // como não há edição de pedido, não há exclusão de produtos de pedido

    // campos preenchíveis
    protected $fillable = [
        'product_id',
        'product_name',
        'original_price',
        'unit_price',
        'quantity',
        'total',
    ];

    // tipos de dados dos campos
    protected $casts = [
        'original_price' => 'double',
        'unit_price' => 'double',
        'quantity' => 'double',
        'total' => 'double',
    ];

    // relação do produto comprado
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
