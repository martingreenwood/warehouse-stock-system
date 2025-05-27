<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'title',
        'description',
        'price',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function stock(): BelongsTo
    {
        return $this->belongsTo(WarehouseStock::class, 'product_uuid', 'uuid');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'product_uuid', 'uuid');
    }

    public function warehouseStock(): HasMany
    {
        return $this->hasMany(WarehouseStock::class, 'product_uuid', 'uuid');
    }

    public function allocatedToOrders(): int
    {
        return $this->orderItems()
            ->join('orders', 'order_items.order_uuid', '=', 'orders.uuid')
            ->where('orders.status', 'placed')
            ->sum('quantity');
    }

    public function totalThreshold(): int
    {
        return $this->warehouseStock()->sum('threshold');
    }

    public function physicalQuantity(): int
    {
        return $this->warehouseStock()->sum('quantity') - $this->allocatedToOrders() - $this->totalThreshold();
    }

    public function immediateDespatch(): int
    {
        return $this->warehouseStock()->sum('quantity') - $this->totalThreshold();
    }
}
