<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Production extends Model
{
    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    protected $table = 'production_batches';
    protected $primaryKey = 'batch_id';

    protected $fillable = [
        'prod_id',
        'batch_number',
        'quantity_produced',
        'production_date',
        'produced_at',
        'expiration_date',
        'baker_id',
        'ingredients_used',
        'status',
        'notes',
        'pending_at',
        'in_progress_at',
        'completed_at',
    ];

    protected $casts = [
        'quantity_produced' => 'integer',
        'production_date' => 'date',
        'produced_at' => 'datetime',
        'expiration_date' => 'date',
        'ingredients_used' => 'array',
        'pending_at' => 'datetime',
        'in_progress_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function getRouteKeyName()
    {
        return 'batch_id';
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'prod_id', 'prod_id');
    }

    public function baker(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'baker_id', 'emp_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Set pending_at timestamp if status is pending
            if ($model->status === self::STATUS_PENDING && empty($model->pending_at)) {
                $model->pending_at = now();
            }
        });

        static::updating(function ($model) {
            // If status changed, update the corresponding timestamp
            if ($model->isDirty('status')) {
                $now = now();
                switch ($model->status) {
                    case self::STATUS_PENDING:
                        $model->pending_at = $now;
                        break;
                    case self::STATUS_IN_PROGRESS:
                        $model->in_progress_at = $now;
                        break;
                    case self::STATUS_COMPLETED:
                        $model->completed_at = $now;
                        break;
                }
            }
        });

        static::created(function ($model) {
            if (class_exists(AuditLog::class)) {
                AuditLog::logAction('create', 'production_batches', $model->batch_id, null, $model->toArray(), "Created production batch {$model->batch_number}");
            }
        });
        static::updated(function ($model) {
            if (class_exists(AuditLog::class)) {
                AuditLog::logAction('update', 'production_batches', $model->batch_id, $model->getOriginal(), $model->getChanges(), "Updated production batch {$model->batch_number}");
            }
        });
        static::deleted(function ($model) {
            if (class_exists(AuditLog::class)) {
                AuditLog::logAction('delete', 'production_batches', $model->batch_id, $model->toArray(), null, "Deleted production batch {$model->batch_number}");
            }
        });
    }

    /**
     * Helper: Get the next status based on interval logic.
     * Example: after 30 min pending -> in_progress, after 1 hour in_progress -> completed
     */
    public function getNextStatusByInterval(): ?string
    {
        $now = now();
        // For testing: change status after 1 minute
        if ($this->status === self::STATUS_PENDING && $this->pending_at && $now->diffInMinutes($this->pending_at) >= 1) {
            return self::STATUS_IN_PROGRESS;
        }
        if ($this->status === self::STATUS_IN_PROGRESS && $this->in_progress_at && $now->diffInMinutes($this->in_progress_at) >= 1) {
            return self::STATUS_COMPLETED;
        }
        return null;
    }
}