<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $table = 'product_categories';
    protected $primaryKey = 'category_id';
    
    protected $fillable = [
        'name',
        'description',
        'status', // 'active' | 'inactive'
    ];

    /**
     * Relationship with Product model
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'category_id', 'category_id');
    }

    /**
     * Get active categories
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Boot method to log changes
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($category) {
            AuditLog::logAction('create', 'product_categories', $category->category_id, null, $category->toArray(), "Created category: {$category->name}");
        });

        static::updated(function ($category) {
            AuditLog::logAction('update', 'product_categories', $category->category_id, $category->getOriginal(), $category->getChanges(), "Updated category: {$category->name}");
        });

        static::deleted(function ($category) {
            AuditLog::logAction('delete', 'product_categories', $category->category_id, $category->toArray(), null, "Deleted category: {$category->name}");
        });
    }
}
