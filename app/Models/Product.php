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

    /**
     * Get warehouse stock breakdown with detailed information.
     */
    public function getWarehouseStockBreakdown(): array
    {
        return $this->warehouseStock->map(function ($stock) {
            return [
                'warehouse_id'   => $stock->warehouse->id,
                'warehouse_name' => $stock->warehouse->name,
                'warehouse_slug' => $stock->warehouse->slug,
                'quantity'       => $stock->quantity,
                'threshold'      => $stock->threshold,
                'available'      => max(0, $stock->quantity - $stock->threshold),
                'status'         => $this->getWarehouseStockStatus($stock),
                'location'       => $this->formatLocation($stock->warehouse),
                'coordinates'    => [
                    'latitude'  => $stock->warehouse->latitude,
                    'longitude' => $stock->warehouse->longitude,
                ],
            ];
        })->toArray();
    }

    /**
     * Get the stock status for a specific warehouse.
     */
    private function getWarehouseStockStatus($stock): string
    {
        if ($stock->quantity <= 0) {
            return 'empty';
        }

        if ($stock->quantity <= $stock->threshold) {
            return 'low';
        }

        return 'good';
    }

    /**
     * Format warehouse location for display.
     */
    private function formatLocation($warehouse): string
    {
        $location = [];

        if ($warehouse->town) {
            $location[] = $warehouse->town;
        }

        if ($warehouse->county) {
            $location[] = $warehouse->county;
        }

        if ($warehouse->postcode) {
            $location[] = $warehouse->postcode;
        }

        return implode(', ', $location) ?: 'Location not specified';
    }
}
