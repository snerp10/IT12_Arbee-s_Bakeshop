<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    // Primary key
    protected $primaryKey = 'emp_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'address',
        'hire_date',
        'position',
        'status',
        'shift_start',
        'shift_end',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'hire_date' => 'date',
        'salary' => 'decimal:2',
        'shift_start' => 'datetime:H:i',
        'shift_end' => 'datetime:H:i',
    ];

    /**
     * Relationship with User model
     */
    public function user()
    {
        return $this->hasOne(User::class, 'emp_id', 'emp_id');
    }

    /**
     * Get the employee's full name.
     */
    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    /**
     * Check if employee is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
