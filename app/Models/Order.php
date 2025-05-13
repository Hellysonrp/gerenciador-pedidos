<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    // usa soft delete, ou seja, não é realmente deletado do banco e é só marcado como deletado
    use SoftDeletes;

    // campos preenchíveis
    protected $fillable = [
        'customer_name',
        'total',
    ];

    // tipos de dados dos campos
    protected $casts = [
        'total' => 'double',
    ];

    // relação entre pedido e produtos do pedido
    public function products(): HasMany
    {
        return $this->hasMany(OrderProduct::class);
    }
}
