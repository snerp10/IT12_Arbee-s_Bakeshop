<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryMovement extends Model
{
    protected $primaryKey = 'movement_id';

    protected $fillable = [
        'prod_id',
        'transaction_type',
        'quantity',
        'previous_stock',
        'current_stock',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'previous_stock' => 'integer',
        'current_stock' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'prod_id', 'prod_id');
    }
}
