<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryStock extends Model
{
    protected $primaryKey = 'inventory_id';
    
    protected $fillable = [
        'prod_id',
        'quantity',
        'reorder_level',
        'last_counted_at',
        'batch_id',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'reorder_level' => 'integer',
        'last_counted_at' => 'datetime',
    ];

    /**
     * Relationship with Product model
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'prod_id', 'prod_id');
    }

    /**
     * Relationship with ProductionBatch model
     */
    public function productionBatch(): BelongsTo
    {
        return $this->belongsTo(Production::class, 'batch_id', 'batch_id');
    }

    // Logging handled in controller to avoid double updates
}
