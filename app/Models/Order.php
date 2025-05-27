<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{

    protected $fillable = [
        'status',
        'total',
    ];

    protected $casts = [
        'total' => 'decimal:2',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_uuid', 'uuid');
    }
}
