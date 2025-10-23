<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model
{
    protected $table = 'sales_orders';
    protected $primaryKey = 'so_id';
    

    protected $fillable = [
        'order_number',
        'order_type',
        'subtotal',
        'vat_amount',
        'total_amount',
        'cash_given',
        'change',
        'order_date',
        'status',
        'cashier_id',
        'notes',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'cash_given' => 'decimal:2',
        'change' => 'decimal:2',
        'order_date' => 'date',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class, 'so_id', 'so_id');
    }

    public function cashier(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'cashier_id', 'emp_id');
    }

    // Removed automatic audit logging to avoid duplicate records. Controllers now handle logging with formal descriptions.
}
