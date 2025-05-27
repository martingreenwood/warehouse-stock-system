<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Warehouse extends Model
{

    protected $fillable = [
        'name',
        'slug',
        'latitude',
        'longitude',
        'address_1',
        'address_2',
        'town',
        'county',
        'postcode',
        'state_code',
        'country_code',
    ];

    protected $casts = [
        'latitude'  => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    public function stock(): HasMany
    {
        return $this->hasMany(WarehouseStock::class, 'warehouse_uuid', 'uuid');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'warehouse_stock', 'warehouse_uuid', 'product_uuid')->withPivot('quantity', 'threshold');
    }
}
