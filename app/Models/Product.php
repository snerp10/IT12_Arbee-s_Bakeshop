<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
    protected $primaryKey = 'prod_id';
    
    protected $fillable = [
        'category_id',
        'name',
        'sku',
        'description',
        'price',
        'unit',
        'preparation_time',
        'is_available',
        'shelf_life',
        'status',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    'preparation_time' => 'integer',
        'is_available' => 'boolean',
        'shelf_life' => 'integer',
    ];

    /**
     * Relationship with Category model
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }

    /**
     * Relationship with InventoryStock model
     */
    public function inventoryStocks(): HasMany
    {
        return $this->hasMany(InventoryStock::class, 'prod_id', 'prod_id');
    }

    /**
     * Singular relationship to get current inventory stock
     */
    public function inventoryStock(): HasOne
    {
        return $this->hasOne(InventoryStock::class, 'prod_id', 'prod_id');
    }

    /**
     * Relationship with ProductionBatch model
     */
    public function productionBatches(): HasMany
    {
        return $this->hasMany(Production::class, 'prod_id', 'prod_id');
    }

    /**
     * Relationship with OrderItem model
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(SaleItem::class, 'prod_id', 'prod_id');
    }

    /**
     * Get active products
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Get low stock products
     */
    public function scopeLowStock($query)
    {
        return $query->whereHas('inventoryStock', function ($query) {
            $query->whereColumn('quantity', '<=', 'reorder_level');
        });
    }

    /**
     * Check if product is low stock
     */
    public function isLowStock(): bool
    {
        $inventory = $this->inventoryStock;
        return $inventory ? $inventory->quantity <= $inventory->reorder_level : false;
    }

    /**
     * Boot method to log changes
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($product) {
            AuditLog::logAction('create', 'products', $product->prod_id, null, $product->toArray(), "Created product: {$product->name}");
        });

        static::updated(function ($product) {
            AuditLog::logAction('update', 'products', $product->prod_id, $product->getOriginal(), $product->getChanges(), "Updated product: {$product->name}");
        });

        static::deleted(function ($product) {
            AuditLog::logAction('delete', 'products', $product->prod_id, $product->toArray(), null, "Deleted product: {$product->name}");
        });
    }
}
