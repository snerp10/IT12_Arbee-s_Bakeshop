<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    protected $primaryKey = 'audit_id';
    
    protected $fillable = [
        'user_id',
        'action',
        'table_name',
        'record_id',
        'old_values',
        'new_values',
        'description',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    /**
     * Relationship with User model
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Log an admin action
     */
    public static function logAction(string $action, ?string $tableName = null, ?int $recordId = null, ?array $oldValues = null, ?array $newValues = null, ?string $description = null): void
    {
        if (auth()->check()) {
            self::create([
                'user_id' => auth()->user()->user_id,
                'action' => $action,
                'table_name' => $tableName,
                'record_id' => $recordId,
                'old_values' => $oldValues,
                'new_values' => $newValues,
                'description' => $description,
            ]);
        }
    }
}
