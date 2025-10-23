<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleItem extends Model
{
    protected $table = 'order_items';
    protected $primaryKey = 'order_item_id';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = null;

    protected $fillable = [
        'so_id',
        'prod_id',
        'quantity',
        'unit_price',
        'total_price',
        'special_instructions',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class, 'so_id', 'so_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'prod_id', 'prod_id');
    }

    protected static function boot()
    {
        parent::boot();
        static::created(function ($model) {
            if (class_exists(AuditLog::class)) {
                AuditLog::logAction('create', 'order_items', $model->order_item_id, null, $model->toArray(), "Added item to sales order {$model->so_id}");
            }
        });
        static::updated(function ($model) {
            if (class_exists(AuditLog::class)) {
                AuditLog::logAction('update', 'order_items', $model->order_item_id, $model->getOriginal(), $model->getChanges(), "Updated order item {$model->order_item_id}");
            }
        });
        static::deleted(function ($model) {
            if (class_exists(AuditLog::class)) {
                AuditLog::logAction('delete', 'order_items', $model->order_item_id, $model->toArray(), null, "Deleted order item {$model->order_item_id}");
            }
        });
    }
}
